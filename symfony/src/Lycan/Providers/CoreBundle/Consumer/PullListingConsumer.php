<?php

namespace Lycan\Providers\CoreBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use \Ramsey\Uuid\Uuid;
use Symfony\Component\Config\Definition\Exception\Exception;


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
		$batchLogger->setEventGroup( $eventGroup = Uuid::uuid4() );
		
		$batch = isset($message['batch']) ? $message['batch'] : null;
		if($batch) {
			$batchLogger->setBatch( $batch);
		}
		
		
		$providerId = $message['provider'];
		$provider = $this->em->getRepository('\Lycan\Providers\CoreBundle\Entity\ProviderAuthBase')->find($providerId);
		if(!$provider) {
			$batchLogger->warning("Unknown Provider. Not found,", $message);
			return true;
		} else if(!$provider->getPullInProgress() && php_sapi_name() === "cli" ){
			// If not CLI
			if(php_sapi_name() !== "cli") {
				throw new Exception("Provider is not currently in progress");
			}
			$batchLogger->warning("Provider is not recognised as in progress. Terminating Pull Request.", $message );
			$this->logger->warning("Provider is not recognised as in progress. Terminating Pull Request.", $message );
			// Discard the message.
			return true;
		}
		
		$batchLogger->info("Processing Pull Listing", $message);
		
		$providerKey = strtolower( $provider->getProviderName() );
		$client  = $this->container->get('lycan.provider.api.factory')->create($providerKey, $provider);
		$manager = $this->container->get('lycan.provider.manager.factory')->create($providerKey);
		
		// Get Listing
		// Get the Mapping Definitions
		// Import
		$em = $this->em;
		$client ->getListingFull($message['id'])
				->then($manager->getProcessIncomingMappingClosure())
				->then(function($schema) use ($provider) {
					// Get Lycan Importer
					$lycan = $this->container->get("app.importer");
					// We want the import function to do the checks.
					// This avoids duplication around lots of code.
					
					$property = $lycan->import( $schema, $provider, true );
					
					return $property;
				}, function($msg)  use($batchLogger, $provider, $em){
					if(is_string($msg)){
						$batchLogger->warn($msg, $provider->getLogValues() );
					}
					
					if($msg instanceof \Exception){
						$batchLogger->warn($msg->getMessage());
					}
					
					
					
					// $provider->setPullInProgress(false);
					// $em->persist($provider);
					// $em->flush();
					
					if(php_sapi_name() !== "cli") {
						dump($msg);die();
					}
					throw new \Exception("Error pulling property information. Provider has been stopped.");
					
				} )
				->then( function($property) use ($eventGroup, $provider) {
					// We should now attempt to claim all logs with the current eventGroup...
					// And assign them to this property..
					
					$tableName = $this->em->getClassMetadata('CoreBundle:Event')->getTableName();
					$sql = "update " . $tableName . " set property_id = :propertyId, provider_id = :providerId where " . $tableName . ".event_group = :eventGroup";
					$params = array('propertyId'=> (string) $property->getId(), 'providerId' => (string) $provider->getId(), 'eventGroup'=>$eventGroup);
					
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
		
		$this->em->clear();
		return true;
		
		
		
	}
}