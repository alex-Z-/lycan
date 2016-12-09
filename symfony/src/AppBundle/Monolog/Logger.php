<?php

namespace AppBundle\Monolog;

class Logger
{
	public $logger;
	
	public function __construct($logger)
	{
		$this->logger = $logger;
	}
	
	public function __call(  $name , array $arguments){
		return call_user_func_array([$this->logger, $name], $arguments);
		
	}
}