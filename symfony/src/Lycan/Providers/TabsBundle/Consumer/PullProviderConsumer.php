<?php

namespace Lycan\Providers\TabsBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Ramsey\Uuid\Uuid;
use Lycan\Providers\TabsBundle\API\Client;
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
		
		$batchLogger = $this->container->get('app.logger.jobs');
		$batchLogger->setBatch($message['batch']);
		$batchLogger->setEventGroup( Uuid::uuid4() );
		$batchLogger->info("Processing Pull Provider", $message);
		
		//Process picture upload.
		//$msg will be an instance of `PhpAmqpLib\Message\AMQPMessage` with the $msg->body being the data sent over RabbitMQ.
		
		$id = $message['id'];
		// Get Provider..
		
		$provider = $this->em->getRepository('\Lycan\Providers\CoreBundle\Entity\ProviderAuthBase')->find($id);
		if(!$provider->getPullInProgress() ){
			$batchLogger->warning("Provider is not recognised as in progress. Terminating Pull Request.", $message );
			// Discard the message.
			return true;
		}
			
		// Get the provider information.
		// Fetch from provider.
		// Create schemas. Add schemas.
		// Create our incoming processor
		
		$tabs = Client::getInstance();
		$tabs->setAuthProvider($provider);
		$properties = $tabs->fetchAllProperties();
		// We have to update each of the individual properties
		
		$jobsInBatch = count($properties['results']);
		foreach($properties['results'] as $index=>$property){
			$msg = [ "id" => $property['id'], "provider" => $id, "batch" => $message['batch'], "jobsInBatch" => $jobsInBatch, "jobIndex" => $index ];
			$batchLogger->info(sprintf("Sending Property with ID of %s to Queue for fetch.", $property['id']), array_merge( ["input" => $property], $msg ));
			
			$routingKey = "lycan.provider.tabs";
			$this->container->get('lycan.rabbit.producer.pull_listing')->publish(serialize($msg), $routingKey);
		}
		
		$this->em->clear();
		// dump($message);
		
	}
}