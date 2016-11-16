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
			// Logs are inserted as separate SQL statements, separate to the current transactions that may exist within the entity manager.
			$em = $this->_container->get('doctrine')->getEntityManager();
			$conn = $em->getConnection();
			
			$created = date('Y-m-d H:i:s');
			
			$serverData = $record['extra']['serverData'];
			
			$uuid = isset($record['extra']['batch']) ? $record['extra']['batch'] : null;
			$eventGroup = isset($record['extra']['eventGroup']) ? $record['extra']['eventGroup'] : null;
			$input = $record['input'] ?: null;
			$output = $record['output'] ?: null;
			
			
			$created = new \DateTime();
			$item = new Event();
			
			$item->setLog( $record['message'] )
				->setLevel( $record['level'] )
				->setServerData( $serverData )
				->setContext($record['extra']['context'])
				->setModifiedAt( $created )
				->setCreatedAt( $created )
				->setBatch( $uuid ? $em->getReference('Lycan\Providers\CoreBundle\Entity\BatchExecutions', $uuid) : null )
				->setInput($input)
				->setOutput($output)
				->setEventGroup( $eventGroup );
			
			$em = $this->_container->get('doctrine')->getEntityManager();
			$em->persist($item);
			$em->flush();
			
		} catch( \Exception $e ) {
			dump($e);die();
			// Fallback to just writing to php error logs if something really bad happens
			error_log($record['message']);
			error_log($e->getMessage());
		}
		
	}
}