<?php

namespace AppBundle\Monolog;

class ConsoleLogger
{
	public $logger;
	
	public function __construct($logger)
	{
		$this->logger = $logger;
	}
}