<?php

namespace Lycan\Providers\CoreBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Ramsey\Uuid\Uuid;

class PushBrandConsumer implements ConsumerInterface
{
	
	
	
	private $logger; // Monolog-logger.
	private $container;
	private $em;
	// Init:
	public function __construct( $logger, $em )
	{
		$this->logger = $logger->logger;
		$this->em = $em;
		echo "PushBrand is listening...";
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
		$batchLogger->info("Processing Push Brand", $message);
		
		//Process picture upload.
		//$msg will be an instance of `PhpAmqpLib\Message\AMQPMessage` with the $msg->body being the data sent over RabbitMQ.
		
		$id = $message['id'];
		// Get Provider..
		
		
		
		$channel = $this->em->getRepository('AppBundle:ChannelBrand')->find($id);
		if(!$channel->getPushInProgress() ){
			$batchLogger->warning("Channel is not recognised as in progress. Terminating Push Request.", $message );
			// Discard the message.
			return true;
		}
		
		// Get the provider information from the channel.
		// Get all properties on the brand
		// Create a push_listing job for each property to push it to the provider
		$provider = $channel->getProvider();
		$providerKey = strtolower( $provider->getProviderName() );
		$manager = $this->container->get('lycan.provider.manager.factory')->create($providerKey);
		
		$brand = $channel->getBrand();
		$listings = $brand->getProperties();
		$listings->forAll(function ($index, $item){
			
			dump($item);die();
		});
		
		
				
		$this->em->clear();
		// dump($message);
		
	}
}