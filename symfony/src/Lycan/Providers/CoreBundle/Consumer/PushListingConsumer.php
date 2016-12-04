<?php

namespace Lycan\Providers\CoreBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Pristine\Schema\Container;
use \Ramsey\Uuid\Uuid;
use React\Promise\Deferred;
use AppBundle\Entity\Listing;

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
		
		
		$providerId = $message['provider'];
		$provider = $this->em->getRepository('\Lycan\Providers\CoreBundle\Entity\ProviderAuthBase')->find($providerId);
		
		$providerKey = strtolower( $provider->getProviderName() );
		$client  = $this->container->get('lycan.provider.api.factory')->create($providerKey, $provider);
		$manager = $this->container->get('lycan.provider.manager.factory')->create($providerKey);
		
		// Get Listing
		// Get the Mapping Definitions
		// Import
		
	
		
		
		$providerId = $message['provider'];
		$provider = $this->em->getRepository('\Lycan\Providers\CoreBundle\Entity\ProviderAuthBase')->find($providerId);
		$providerKey = strtolower( $provider->getProviderName() );
		
		
		$client  = $this->container->get('lycan.provider.api.factory')->create($providerKey, $provider);
		$manager = $this->container->get('lycan.provider.manager.factory')->create($providerKey);
		$manager->setClient($client);
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
		
		// This is where things get tricky.
		// A provider might be like Rentivo and can recieve the property in a SINGLE request.
		// Although this is unlikely. We may need to create an abstract "TRANSPORT" object which envelops a batch job process.
		// Something like "create" property. Get ID. Push images. Push descriptions. etc.
		// We'll refactor when we know how other systems do it.
		$deferred->promise()
			->then(function($schema) use ($manager, $listing){
				// If we can pass on credentials. Do a mixin before passing on.
				if($listing->getProvider()->getPassOnCredentials()){
					// If passthrough provider is set. We can receive this in the process outgoing.
					$manager->setPassThroughProvider($listing->getProvider());
				}
				return $schema;
			})
			->then($manager->getProcessOutgoingMappingClosure())
			->then(function($model) use ($manager, $listing, $provider){
				// This will insert/update the lycan model
				$listings = $this->em->getRepository("AppBundle:Property")->findListingsByProvider($provider, $listing);
				// We're assuming we only have a single channel listing for now, but this might change. This will need refactoring.
				$channelListing =  ($listings && $listings->count() >= 1) ? $listings->current() : null;
				// If we know what the CHANNEL LISTING is, we pass that to the upsert. Because then we can UPDATE. Rather than insert.
				
				$id = $manager->upsert($model, $channelListing);
				// If NOT NULL
				// TEST WHAT HAPPENS
				if($id){
					// Now we create a child listing.
					$channelListing = $channelListing?: new Listing();
					$channelListing->setProvider($provider);
					$channelListing->setProviderListingId($id);
					$channelListing->setSchemaObject($model->toJson());
					$channelListing->setMaster($listing);
					$channelListing->setDescriptiveName($model->get("name"));
					$channelListing->setIsSchemaValid($listing->getIsSchemaValid());
					$this->em->persist($channelListing);
					$this->em->persist($listing);
					$this->em->flush();
					return $channelListing;
				} else {
					throw new \Exception("Could not insert, missing ID from upsert.");
				}
				
			})->then( function($property) use ($eventGroup) {
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
					$provider->setPushInProgress(false);
					$this->em->persist($provider);
					$this->em->flush();
				}
			});
		
		return true;
		// $this->em->clear();
		
		
	}
}