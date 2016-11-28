<?php

namespace Lycan\Providers\RentivoBundle\API;
use Incoming\Processor;
use Lycan\Providers\CoreBundle\API\ManagerInterface;
use Pristine\Schema\Container as SchemaContainer;
use Lycan\Providers\RentivoBundle\Incoming\Hydrator\SchemaHydrator as Hydrator;
use Lycan\Providers\RentivoBundle\Incoming\Transformer\RentivoTransformer;


class Manager implements ManagerInterface {
	
	private $container;
	public $message;
	public $provider;
	
	public function setContainer($container){
		$this->container = $container;
	}
	
	
	public function getQueuePullListingsClosure(){
		return function($listings) {
			$logger = $this->container->get('app.logger.jobs');
			$jobsInBatch = count($listings['data']);
			$message = $this->getMessage();
			foreach($listings['data'] as $index=>$listing){
				$msg = [ "id" => $listing['id'], "provider" => (string) $this->getProvider()->getId() , "batch" => $message['batch'], "jobsInBatch" => $jobsInBatch, "jobIndex" => $index ];
				$logger->info(sprintf("Sending Property with ID of %s to Queue for fetch.", $listing['id']), array_merge( ["input" => $listing], $msg ));
				
				$routingKey = "lycan.provider.rentivo";
				$this->container->get('lycan.rabbit.producer.pull_listing')->publish(serialize($msg), $routingKey);
			}
			
		};
	}
	
	
	public function getProcessMappingClosure(){
		
		return function($data) {
			
			$incoming = new Processor(new RentivoTransformer());
			$schema   = $incoming->process(
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