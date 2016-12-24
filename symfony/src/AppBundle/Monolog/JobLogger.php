<?php

namespace AppBundle\Monolog;

use AppBundle\Monolog\Processor\JobProcessor;

class JobLogger
{
	public $logger;
	
	public function __construct($logger)
	{
		$this->logger = $logger;
	}
	
	public function setBatch($batch){
		
		foreach($this->logger->getProcessors() as $processor){
			
			if($processor[0] instanceof JobProcessor){
				$processor[0]->setBatch($batch);
			}
			
		}
		
	}
	
	public function setEventGroup($eventGroup){
		
		
		foreach($this->logger->getProcessors() as $processor){
			
			if($processor[0] instanceof JobProcessor){
				$processor[0]->setEventGroup($eventGroup);
			}
			
		}
		
	}
	
	
	public function getEventGroup(){
		foreach($this->logger->getProcessors() as $processor){
			
			if($processor[0] instanceof JobProcessor){
				return $processor[0]->getEventGroup();
			}
			
		}
	}
	
	public function __call(  $name , array $arguments){
		try {
			return call_user_func_array([$this->logger, $name], $arguments);
		} catch(\Exception $e){
			return null;
		}
		
	}
}