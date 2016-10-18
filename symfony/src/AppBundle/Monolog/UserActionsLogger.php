<?php

namespace AppBundle\Monolog;

class UserActionsLogger
{
	public $logger;
	
	public function __construct($logger)
	{
		$this->logger = $logger;
	}
}