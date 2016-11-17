<?php

namespace Lycan\Providers\CoreBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use \Ramsey\Uuid\Uuid;


class PullListingConsumer implements ConsumerInterface
{
	
	private $logger; // Monolog-logger.
	private $container;
	private $em;
	// Init:
	public function __construct( $logger, $em )
	{
		$this->logger = $logger->logger;
		$this->em = $em;
		echo "PullListing is listening...";
	}
	
	public function setContainer($container){
		$this->container = $container;
	}
			
	public function execute(AMQPMessage $msg)
	{
	
		$message = unserialize($msg->body);
		$this->logger->info("Processing Pull Listing", $message);
		
		$batchLogger = $this->container->get('app.logger.jobs');
		$batchLogger->setBatch($message['batch']);
		$batchLogger->setEventGroup( $eventGroup = Uuid::uuid4() );
		$batchLogger->info("Processing Pull Listing", $message);
		
		
		$providerId = $message['provider'];
		$provider = $this->em->getRepository('\Lycan\Providers\CoreBundle\Entity\ProviderAuthBase')->find($providerId);
		if(!$provider) {
			$batchLogger->warning("Unknown Provider. Not found,", $message);
			return true;
		} else if(!$provider->getPullInProgress() ){
			
			$batchLogger->warning("Provider is not recognised as in progress. Terminating Pull Request.", $message );
			$this->logger->warning("Provider is not recognised as in progress. Terminating Pull Request.", $message );
			// Discard the message.
			return true;
		}
		
		$providerKey = strtolower( $provider->getProviderName() );
		$client  = $this->container->get('lycan.provider.api.factory')->create($providerKey, $provider);
		$manager = $this->container->get('lycan.provider.manager.factory')->create($providerKey);
		
		// Get Listing
		// Get the Mapping Definitions
		// Import
		$client ->getListingFull($message['id'])
				->then($manager->getProcessMappingClosure())
				->then(function($schema) use ($provider) {
					// Get Lycan Importer
					$lycan = $this->container->get("app.importer");
					// We want the import function to do the checks.
					// This avoids duplication around lots of code.
					$property = $lycan->import( $schema, $provider );
					return $property;
				} )
				->then( function($property) use ($eventGroup) {
					// We should now attempt to claim all logs with the current eventGroup...
					// And assign them to this property..
					$tableName = $this->em->getClassMetadata('CoreBundle:Event')->getTableName();
					$sql = "update " . $tableName . " set property_id = :propertyId where " . $tableName . ".event_group = :eventGroup";
					$params = array('propertyId'=> (string) $property->getId(), 'eventGroup'=>$eventGroup);
					
					$stmt = $this->em->getConnection()->prepare($sql);
					$stmt->execute($params);
				})->then( function() use ($message, $provider){
					if($message['jobsInBatch'] === $message['jobIndex']){
						// We can close as it's finished..
						$provider->setPullInProgress(false);
						$this->em->persist($provider);
						$this->em->flush();
					}
				});
		
		
		return true;
		// $this->em->clear();
		
		
	}
}