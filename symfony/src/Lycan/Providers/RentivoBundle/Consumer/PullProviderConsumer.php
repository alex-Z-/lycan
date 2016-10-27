<?php

namespace Lycan\Providers\RentivoBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

use Lycan\Providers\RentivoBundle\API\Client;
class PullProviderConsumer implements ConsumerInterface
{
	
	private $logger; // Monolog-logger.
	private $container;
	private $em;
	// Init:
	public function __construct( $logger, $em )
	{
		$this->logger = $logger->logger;
		$this->em = $em;
		echo "PullProvider is listening...";
	}
	
	public function setContainer($container){
		$this->container = $container;
	}
	
	public function execute(AMQPMessage $msg)
	{
		
		$message = unserialize($msg->body);
		$this->logger->info("Processing Pull Provider", $message);
		//Process picture upload.
		//$msg will be an instance of `PhpAmqpLib\Message\AMQPMessage` with the $msg->body being the data sent over RabbitMQ.
		
		$id = $message['id'];
		// Get Provider..
		
		$provider = $this->em->getRepository('\Lycan\Providers\CoreBundle\Entity\ProviderAuthBase')->find($id);
		if(!$provider->getPullInProgress() ){
			$this->logger->warning("Provider is not recognised as in progress. Terminating Pull Request.", $message );
			// Discard the message.
			return true;
		}
			
		// Get the provider information.
		// Fetch from provider.
		// Create schemas. Add schemas.
		// Create our incoming processor
		
		$rentivo = Client::getInstance();
		$rentivo->setAuthProvider($provider);
		$properties = $rentivo->fetchAllProperties();
		// We have to update each of the individual properties
		
		foreach($properties['data'] as $property){
		
			$this->logger->info( sprintf("Sending Property with ID of %s to Queue for fetch.", $property['id']), $property );
			$msg = [ "id" => $property['id'], "provider" => $id ];
			$routingKey = sprintf("lycan.provider.rentivo", $provider);
			$this->container->get('lycan.rabbit.producer.pull_listing')->publish(serialize($msg), $routingKey);
		}
		
		$this->em->clear();
		// dump($message);
		
	}
}