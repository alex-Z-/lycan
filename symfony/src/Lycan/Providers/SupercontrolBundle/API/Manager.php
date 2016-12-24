<?php

namespace Lycan\Providers\SupercontrolBundle\API;
use Incoming\Processor;
use Lycan\Providers\CoreBundle\API\SourceMappingResult;
use Lycan\Providers\CoreBundle\Entity\BatchExecutions;
use Pristine\Schema\Container as SchemaContainer;
use Lycan\Providers\CoreBundle\API\ManagerInterface;
use Lycan\Providers\SupercontrolBundle\Incoming\Hydrator\SchemaHydrator as Hydrator;
use Lycan\Providers\SupercontrolBundle\Incoming\Transformer\Transformer;

class Manager  implements ManagerInterface {
	
	
	private $container;
	private $message;
	private $provider;
	
	public function setContainer($container){
		$this->container = $container;
	}
	
	public function getQueuePushListingsClosure()
	{
		// TODO: Implement getQueuePushListingsClosure() method.
	}
	
	public function getProcessOutgoingMappingClosure()
	{
		// TODO: Implement getProcessOutgoingMappingClosure() method.
	}
	
	
	public function getQueuePullProviderClosure(){
		
		return function(ProviderAuthBase $object, Property $property = null) {
			$em = $this->container->get('doctrine')
				->getEntityManager();
			
			
			$object->setPullInProgress(true);
			
			$batch = new BatchExecutions();
			$batch->setProvider($object);
			$object->setLastActiveBatch($batch);
			$em->persist($object);
			$em->persist($batch);
			$em->flush();
			
			$logger = $this->container->get('app.logger.jobs');
			$logger->setBatch($batch->getId());
			$logger->debug("Creating a new batch execution job");
			
			$logger = $this->container->get('app.logger.user_actions')->logger;
			$logger->info('Manual initiation of pull syncronization', ['provider' => $object->getId(), "nickname" => $object->getNickname()]);
			
			// Add
			$msg      = ["id" => $object->getId(), "batch" => $batch->getId()];
			
			if($property){
				$msg['property'] = (string) $property->getId();
			}
			
			$code     = strtolower($object->getProviderName());
			$routingKey = sprintf("lycan.provider.pull.provider.%s", $code);
			$this->container->get('lycan.rabbit.producer.pull_provider')->publish(serialize($msg), $routingKey);
		};
		
		
	}
	
	
	public function getQueuePullListingsClosure(){
		return function($listings){
			
			$logger = $this->container->get('app.logger.jobs');
			$message = $this->getMessage();
			$jobsInBatch = count($listings);
			
			 
			foreach($listings as $index=>$listing){
				
				$msg = [ "id" => $listing['@id'], "provider" => $this->getProvider()->getId(), "batch" => (string) $message['batch'], "jobsInBatch" => $jobsInBatch, "jobIndex" => $index + 1 ];
				
				$logger->info(sprintf("Sending Property with ID of %s to Queue for fetch.", $listing['@id']), array_merge( ["input" => $listing], $msg ));
				try {
					$routingKey = "lycan.provider.pull.listing.supercontrol";
					$this->container->get('lycan.rabbit.producer.pull_listing')
						->publish(serialize($msg), $routingKey);
				}catch(\Exception $e){
					// TODO need to show a log?
				}
				
				
			}
			
		};
	}
	
	public function getProcessIncomingMappingClosure(){
		$container = $this->container;

		return function($data) use($container) {
			
			$incoming = new Processor(new Transformer());
			
			$schema = $incoming->process(
				$data,
				new SchemaContainer(),
				new Hydrator($container)
			);
			// Set the data and the schema.
			return new SourceMappingResult($data, $schema);
		};
		
	}
	
	/**
	 * @return mixed
	 */
	public function getMessage()
	{
		return $this->message;
	}
	
	/**
	 * @param mixed $message
	 */
	public function setMessage($message)
	{
		$this->message = $message;
	}
	
	/**
	 * @return mixed
	 */
	public function getProvider()
	{
		return $this->provider;
	}
	
	/**
	 * @param mixed $provider
	 */
	public function setProvider($provider)
	{
		$this->provider = $provider;
	}
	
	
	
	
	
	
	
	
}