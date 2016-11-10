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
	
	public function __call(  $name , array $arguments){
			return call_user_func_array([$this->logger, $name], $arguments);
		
	}
}