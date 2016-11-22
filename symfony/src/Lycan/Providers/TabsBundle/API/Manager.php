<?php

namespace Lycan\Providers\TabsBundle\API;
use Incoming\Processor;
use Pristine\Schema\Container as SchemaContainer;
use Lycan\Providers\CoreBundle\API\ManagerInterface;
use Lycan\Providers\TabsBundle\Incoming\Hydrator\SchemaHydrator as Hydrator;
use Lycan\Providers\TabsBundle\Incoming\Transformer\TabsTransformer;

class Manager  implements ManagerInterface {
	
	
	private $container;
	private $message;
	private $provider;
	public function setContainer($container){
		$this->container = $container;
	}
	
	public function getQueuePullListingsClosure(){
		return function($listings){
			$logger = $this->container->get('app.logger.jobs');
			$message = $this->getMessage();
			$jobsInBatch = count($listings['results']);
			foreach($listings['results'] as $index=>$listing){
				$msg = [ "id" => $listing['id'], "provider" => $this->getProvider()->getId(), "batch" => $message['batch'], "jobsInBatch" => $jobsInBatch, "jobIndex" => $index + 1 ];
				$logger->info(sprintf("Sending Property with ID of %s to Queue for fetch.", $listing['id']), array_merge( ["input" => $listing], $msg ));
				
				$routingKey = "lycan.provider.tabs";
				$this->container->get('lycan.rabbit.producer.pull_listing')->publish(serialize($msg), $routingKey);
			}
			
		};
	}
	
	public function getProcessMappingClosure(){
		
		return function($data) {
			
			$incoming = new Processor(new TabsTransformer());
			
			$schema = $incoming->process(
				$data,
				new SchemaContainer(),
				new Hydrator()
			);
			return $schema;
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