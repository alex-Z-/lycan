<?php

namespace AppBundle\Monolog;

class Logger
{
	public $logger;
	
	public function __construct($logger)
	{
		$this->logger = $logger;
	}
}