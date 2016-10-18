<?php

namespace Lycan\Providers\RentivoBundle\Consumer;

use AppBundle\AppBundle;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Incoming;
use AppBundle\Schema\Container as SchemaContainer;
use Lycan\Providers\RentivoBundle\Transformer\SchemaHydrator;
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
		
		$client   = $this->container->get('guzzle.client.rentivo');
		$response = $client->get('/api/public/properties/schemas/52021');
		$result = json_decode ( (string) $response->getBody(), true );
		$data = $result['data'];
	
		$incoming = new Incoming\Processor();
		$schema = $incoming->process(
			$data,
			new SchemaContainer(),
			new SchemaHydrator()
		);
		dump($schema);die();
		
		
		
		
		// dump($message);
		
	}
}