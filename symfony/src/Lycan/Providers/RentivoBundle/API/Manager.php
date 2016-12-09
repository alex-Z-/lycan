<?php

namespace Lycan\Providers\RentivoBundle\API;
use AppBundle\Entity\Listing;
use AppBundle\Entity\Property;
use Doctrine\Common\Collections\ArrayCollection;
use Incoming\Processor;
use Lycan\Providers\CoreBundle\API\ManagerInterface;
use Pristine\Schema\Container as SchemaContainer;
use Lycan\Providers\RentivoBundle\Incoming\Hydrator\SchemaHydrator as IncomingHydrator;
use Lycan\Providers\RentivoBundle\Outgoing\Hydrator\SchemaHydrator as OutgoingHydrator;
use Lycan\Providers\RentivoBundle\Incoming\Transformer\RentivoTransformer as IncomingTransformer;
use Lycan\Providers\RentivoBundle\Outgoing\Transformer\RentivoTransformer as OutgoingTransformer;



class Manager implements ManagerInterface {
	
	private $container;
	public $message;
	public $provider;
	public $passThroughProvider;
	public $client;
	
	public function setContainer($container){
		$this->container = $container;
	}
	
	
	public function getQueuePullProviderClosure(){
		
		return function($object) {
			$em = $this->container->get('doctrine')
				->getEntityManager();
			
			
			$object->setPullInProgress(true);
			$batch = new BatchExecutions();
			$batch->setProvider($object);
			$object->setLastActiveBatch($batch);
			$em->persist($object);
			$em->persist($batch);
			$em->flush();
			
			$logger = $this->container->get('app.logger.jobs');
			$logger->setBatch($batch->getId());
			$logger->debug("Creating a new batch execution job");
			
			$logger = $this->container->get('app.logger.user_actions')->logger;
			$logger->info('Manual initiation of pull syncronization', ['provider' => $object->getId(), "nickname" => $object->getNickname()]);
			
			// Add
			$msg      = ["id" => $object->getId(), "batch" => $batch->getId()];
			$code     = strtolower($object->getProviderName());
			$routingKey = sprintf("lycan.provider.pull.provider.%s", $code);
			$this->container->get('lycan.rabbit.producer.pull_provider')->publish(serialize($msg), $routingKey);
		};
		
		
	}
	
	
	public function getQueuePullListingsClosure(){
		return function($listings) {
			$logger = $this->container->get('app.logger.jobs');
			$jobsInBatch = count($listings['data']);
			$message = $this->getMessage();
			foreach($listings['data'] as $index=>$listing){
				$msg = [ "id" => $listing['id'], "provider" => (string) $this->getProvider()->getId() , "batch" => (string) $message['batch'], "jobsInBatch" => $jobsInBatch, "jobIndex" => $index + 1 ];
				$logger->info(sprintf("Sending Property with ID of %s to Queue for fetch.", $listing['id']), array_merge( ["input" => $listing], $msg ));
				
				$routingKey = "lycan.provider.pull.listing.rentivo";
				$this->container->get('lycan.rabbit.producer.pull_listing')->publish(serialize($msg), $routingKey);
			}
			
		};
	}
	
	public function getQueuePushListingsClosure(){
	
		return function( $listings) {
			
			$logger = $this->container->get('app.logger.jobs');
			$jobsInBatch = $listings->count();
			$message = $this->getMessage();
			
			foreach($listings as $index=>$listing){
			
				$msg = [ "id" => (string) $listing->getId(), "provider" => (string) $this->getProvider()->getId() , "batch" => (string) $message['batch'], "jobsInBatch" => $jobsInBatch, "jobIndex" => $index + 1  ];
				$logger->info(sprintf("Sending Property with ID of %s to Queue for Push Syncronization.",  (string) $listing->getId() ),  $msg );
				
				$routingKey = "lycan.provider.push.listing.rentivo";
				$this->container->get('lycan.rabbit.producer.push_listing')->publish(serialize($msg), $routingKey);
			}
			
		};
	}
	
	public function getProcessOutgoingMappingClosure(){
		// Receives a Lycan Schema
		return function($json) {
			
			// For Rentivo, it is a single JSON. This will probabaly need refactoring when we
			// start pushing different requests to different providers. Likely there will be multi-batch transactions
			// or multi request that we need to take into account. 
			$outgoing = new Processor(new OutgoingTransformer());
			
			$schema   = $outgoing->process(
				$json,
				new SchemaContainer(),
				new OutgoingHydrator($this->getPassThroughProvider())
			);
			return $schema;
		};
		
	}
	
	
	public function getProcessIncomingMappingClosure(){
		
		return function($data) {
			
			$incoming = new Processor(new IncomingTransformer());
			$schema   = $incoming->process(
				$data,
				new SchemaContainer(),
				new IncomingHydrator()
			);
			return $schema;
		};
		
	}
	
	// Used to take some data and push to remote provider
	public function upsert(SchemaContainer $model, Listing $listing = null){
		$client = $this->getClient();
		
		$endpoint = "/api/r/properties/schemas";
		if($listing){
			$endpoint = sprintf( "/api/r/properties/schemas/%s", $listing->getProviderListingId() );
			$response = $client->getClient()->put( $endpoint,  [ 'json' => $model->toArray() ]);
		} else {
			$response = $client->getClient()->post( $endpoint,  [ 'json' => $model->toArray() ]);
		}
		// In order to upsert, we have to push to Rentivo and if we KNOW what the ID is on Rentivo, we should use that ID.
		// Otherwise we can just POST as a new property.
		$data = json_decode((string) $response->getBody(), true);
		
		if($response->getStatusCode() === 200){
			
			// This is the ID of the inserted property.
			$id = $data['data'][0]['id'];
			$logger = $this->container->get('app.logger.jobs');
			
			$logger->warning("Successfully upserted Listing on Rentivo.", ["id"=> $id]);
			
			return $id;
		} else {
			// Failed to insert. We should log this..
			$logger = $this->container->get('app.logger.jobs');
			$data = json_decode((string) $response->getBody(), true);
			$logger->warning(sprintf("Error when trying to push rental to Rentivo. Returned a %s status code.", $response->getStatusCode()), [ "endpoint" => $endpoint, "input" => $model->toArray(), "output"=> $data ]);
	
			return null;
		}
	
	}
	
	/**
	 * @return mixed
	 */
	public function getMessage()
	{
		return $this->message;
	}
	
	/**
	 * @param mixed $message
	 */
	public function setMessage($message)
	{
		$this->message = $message;
	}
	
	/**
	 * @return mixed
	 */
	public function getProvider()
	{
		return $this->provider;
	}
	
	/**
	 * @param mixed $provider
	 */
	public function setProvider($provider)
	{
		$this->provider = $provider;
	}
	
	/**
	 * @return mixed
	 */
	public function getPassThroughProvider()
	{
		return $this->passThroughProvider;
	}
	
	/**
	 * @param mixed $passThroughProvider
	 */
	public function setPassThroughProvider($passThroughProvider)
	{
		$this->passThroughProvider = $passThroughProvider;
	}
	
	
	
	/**
	 * @return mixed
	 */
	public function getClient()
	{
		return $this->client;
	}
	
	/**
	 * @param mixed $client
	 */
	public function setClient($client)
	{
		$this->client = $client;
	}
	
	
	
}