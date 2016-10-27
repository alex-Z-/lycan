<?php

namespace Lycan\Providers\RentivoBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Incoming;

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
		
		$providerId = $message['provider'];
		$provider = $this->em->getRepository('\Lycan\Providers\CoreBundle\Entity\ProviderAuthBase')->find($providerId);
		if(!$provider->getPullInProgress() ){
			$this->logger->warning("Provider is not recognised as in progress. Terminating Pull Request.", $message );
			// Discard the message.
			return true;
		}
		
		// Rentivo API Client
		$rentivo = Client::getInstance();
		$rentivo->setAuthProvider($provider);
		$data =  $rentivo->getPropertyFull($message['id']);
		// Get Lycan Importer
		$lycan = $this->container->get("app.importer");
		// We want the import function to do the checks.
		// This avoids duplication around lots of code.
		$incoming = new Incoming\Processor(  new RentivoTransformer() );
		$schema = $incoming->process(
			$data,
			new SchemaContainer(),
			new Hydrator()
		);
		
		$lycan->import( $schema, $provider );
		
		// $this->em->clear();
		
		
	}
}