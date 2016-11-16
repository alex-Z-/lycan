<?php

namespace Lycan\Providers\TabsBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Incoming;
use \Ramsey\Uuid\Uuid;
use Pristine\Schema\Container as SchemaContainer;
use Lycan\Providers\RentivoBundle\Incoming\Hydrator\SchemaHydrator as Hydrator;
use Lycan\Providers\RentivoBundle\Incoming\Transformer\RentivoTransformer;
use Lycan\Providers\RentivoBundle\API\Client;
use AppBundle\Importer\Importer as Importer;

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
		if(!$provider->getPullInProgress() ){
			
			$batchLogger->warning("Provider is not recognised as in progress. Terminating Pull Request.", $message );
			$this->logger->warning("Provider is not recognised as in progress. Terminating Pull Request.", $message );
			// Discard the message.
			return true;
		}
		
		// Rentivo API Client
		$tabs = Client::getInstance();
		$tabs->setAuthProvider($provider);
		$data =  $tabs->getPropertyFull($message['id']);
		$batchLogger->info("Fetch Property Listing from Provider", [ "input" => $data ] );
		// Get Lycan Importer
		$lycan = $this->container->get("app.importer");
		// We want the import function to do the checks.
		// This avoids duplication around lots of code.
		$incoming = new Incoming\Processor(  new TabsTransformer() );
		$schema = $incoming->process(
			$data,
			new SchemaContainer(),
			new Hydrator()
		);
		
		$property = $lycan->import( $schema, $provider );
	
		// We should not attempt to claim all logs with the current eventGroup...
		// And assign them to this property..
		// $eventGroup = $batchLogger->getEventGroup();
		
		$tableName = $this->em->getClassMetadata('CoreBundle:Event')->getTableName();
		$sql = "update " . $tableName . " set property_id = :propertyId where " . $tableName . ".event_group = :eventGroup";
		$params = array('propertyId'=> (string) $property->getId(), 'eventGroup'=>$eventGroup);
		
		$stmt = $this->em->getConnection()->prepare($sql);
		$stmt->execute($params);
				
		
		return false;
		// $this->em->clear();
		
		
	}
}