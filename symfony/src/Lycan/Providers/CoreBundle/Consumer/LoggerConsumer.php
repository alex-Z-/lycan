<?php

namespace Lycan\Providers\CoreBundle\Consumer;

use Lycan\Providers\CoreBundle\Entity\Event;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use \Ramsey\Uuid\Uuid;
use Symfony\Component\Config\Definition\Exception\Exception;


class LoggerConsumer implements ConsumerInterface
{
	
	private $logger; // Monolog-logger.
	private $container;
	private $em;
	// Init:
	public function __construct( $logger, $em )
	{
		$this->logger = $logger->logger;
		$this->em = $em;
		echo "Logger is listening...";
	}
	
	public function setContainer($container){
		$this->container = $container;
	}
			
	public function execute(AMQPMessage $msg)
	{
		echo "Received message\n";
		$record = unserialize($msg->body);
		// Logs are inserted as separate SQL statements, separate to the current transactions that may exist within the entity manager.
		$em = $this->container->get('doctrine')->getManager();
		
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
		
		
		$em->persist($item);
		$em->flush();
		
		return true;
	}
}