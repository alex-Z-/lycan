<?php

namespace Lycan\Providers\CoreBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Pristine\Schema\Container;
use \Ramsey\Uuid\Uuid;
use React\Promise\Deferred;


class PushListingConsumer implements ConsumerInterface
{
	
	private $logger; // Monolog-logger.
	private $container;
	private $em;
	// Init:
	public function __construct( $logger, $em )
	{
		$this->logger = $logger->logger;
		$this->em = $em;
		echo "PushListing is listening...";
	}
	
	public function setContainer($container){
		$this->container = $container;
	}
			
	public function execute(AMQPMessage $msg)
	{
	
		$message = unserialize($msg->body);
		$this->logger->info("Processing Push Listing", $message);
		
		$batchLogger = $this->container->get('app.logger.jobs');
		$batchLogger->setBatch($message['batch']);
		$batchLogger->setEventGroup( $eventGroup = Uuid::uuid4() );
		$batchLogger->info("Processing Push Listing", $message);
		
		dump($message);die();
		$providerId = $message['provider'];
		$provider = $this->em->getRepository('\Lycan\Providers\CoreBundle\Entity\ProviderAuthBase')->find($providerId);
		if(!$provider) {
			$batchLogger->warning("Unknown Provider. Not found,", $message);
			return true;
		}
		
		$providerKey = strtolower( $provider->getProviderName() );
		$client  = $this->container->get('lycan.provider.api.factory')->create($providerKey, $provider);
		$manager = $this->container->get('lycan.provider.manager.factory')->create($providerKey);
		
		// Get Listing from Lycan
		
		$listing = $this->em->getRepository("AppBundle:Property")->find($message['id']);
		
		if(!$listing->getIsSchemaValid()){
			$batchLogger->warning("Schema is not valid. Cannot export and syncronization to external channel.", $message);
			return true;
		}
		$schema = $listing->getSchemaObject();
		// $schemaContainer = new Container(json_decode( $schema, true));
		// $schemaContainer->fromArray();
		
		$deferred = new Deferred();
		$deferred->resolve($schema);
		$deferred->promise()
			->then($manager->getProcessOutgoingMappingClosure());
		
		
		return true;
		// $this->em->clear();
		
		
	}
}