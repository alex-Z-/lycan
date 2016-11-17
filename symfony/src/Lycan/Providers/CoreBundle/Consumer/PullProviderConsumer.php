<?php

namespace Lycan\Providers\CoreBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Ramsey\Uuid\Uuid;

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
		
		$providerKey = strtolower( $provider->getProviderName() );
		$client = $this->container->get('lycan.provider.api.factory')->create($providerKey, $provider);
		// The manager is what controls HOW things are done.
		// The client is the actual API client for each provider.
		$manager = $this->container->get('lycan.provider.manager.factory')->create($providerKey);
		$manager->setMessage($message);
		$manager->setProvider($provider);
		$client->fetchAllListings()
				->then($manager->getQueuePullListingsClosure());
				
		$this->em->clear();
		// dump($message);
		
	}
}