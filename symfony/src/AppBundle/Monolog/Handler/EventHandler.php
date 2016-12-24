<?php

namespace AppBundle\Monolog\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Lycan\Providers\CoreBundle\Entity\Event;
/**
 * Stores to database
 *
 */
class EventHandler extends AbstractProcessingHandler
{
	protected $_container;
	
	/**
	 * @param string $stream
	 * @param integer $level The minimum logging level at which this handler will be triggered
	 * @param Boolean $bubble Whether the messages that are handled can bubble up the stack or not
	 */
	public function __construct($level = Logger::DEBUG, $bubble = true)
	{
		parent::__construct($level, $bubble);
	}
	
	/**
	 *
	 * @param type $container
	 */
	public function setContainer($container)
	{
		$this->_container = $container;
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function write(array $record)
	{
		// Ensure the doctrine channel is ignored (unless its greater than a warning error), otherwise you will create an infinite loop, as doctrine like to log.. a lot..
		try
		{
			
			$msg = $record;
			$this->_container->get('lycan.rabbit.producer.logger')->publish(serialize($msg));
			return true;
			
			
			
			
		} catch( \Exception $e ) {
			
		}
		
	}
}