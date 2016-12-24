<?php

namespace Lycan\Providers\RentivoBundle\API;
use AppBundle\Entity\Listing;
use AppBundle\Entity\Property;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Incoming\Processor;
use Lycan\Providers\CoreBundle\API\ManagerInterface;
use Lycan\Providers\CoreBundle\API\SourceMappingResult;
use Pristine\Schema\Container as SchemaContainer;
use Lycan\Providers\RentivoBundle\Incoming\Hydrator\SchemaHydrator as IncomingHydrator;
use Lycan\Providers\RentivoBundle\Outgoing\Hydrator\SchemaHydrator as OutgoingHydrator;
use Lycan\Providers\RentivoBundle\Incoming\Transformer\RentivoTransformer as IncomingTransformer;
use Lycan\Providers\RentivoBundle\Outgoing\Transformer\RentivoTransformer as OutgoingTransformer;



class Manager implements ManagerInterface {
	
	private $container;
	public $batch = null;
	public $provider;
	public $passThroughProvider;
	public $client;
	
	public function setContainer($container){
		$this->container = $container;
	}
	
	
	public function getQueuePullProviderClosure(){
		
		return function(ProviderAuthBase $object, Property $property = null) {
			$em = $this->container->get('doctrine')
				->getManager();
			
			
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
			if($property){
				$msg['property'] = (string) $property->getId();
			}
			$code     = strtolower($object->getProviderName());
			$routingKey = sprintf("lycan.provider.pull.provider.%s", $code);
			$this->container->get('lycan.rabbit.producer.pull_provider')->publish(serialize($msg), $routingKey);
		};
		
		
	}
	
	
	public function getQueuePullListingsClosure(){
		return function($listings) {
			$logger = $this->container->get('app.logger.jobs');
			$jobsInBatch = count($listings['data']);
			
			foreach($listings['data'] as $index=>$listing){
				$msg = [ "id" => $listing['id'], "provider" => (string) $this->getProvider()->getId() , "batch" => (string) $this->getBatch()->getId(), "jobsInBatch" => $jobsInBatch, "jobIndex" => $index + 1 ];
				$logger->info(sprintf("Sending Property with ID of %s to Queue for fetch.", $listing['id']), array_merge( ["input" => $listing], $msg ));
				
				$routingKey = "lycan.provider.pull.listing.rentivo";
				$this->container->get('lycan.rabbit.producer.pull_listing')->publish(serialize($msg), $routingKey);
			}
			
		};
	}
	
	public function getQueuePushListingsClosure(){
	
		return function( PersistentCollection $listings, $channelId, $jobsInBatch = null, $offset = null) {
			
			$logger = $this->container->get('app.logger.jobs');
			$jobsInBatch = !$jobsInBatch ? $listings->count() : $jobsInBatch;
			$offset = !$offset ? 0 : $offset;
			
			
			// Partitions...
			$partitionChunkSize = 100;
			$partitionsNeeded = (int) round( ceil( $listings->count() / $partitionChunkSize ), 0 );
			foreach(range(0, $partitionsNeeded) as $partIndex => $p){
				foreach($listings->slice($partIndex * $partitionChunkSize, $partitionChunkSize) as $index => $listing){
				
					$index = $index + ( $partIndex * $partitionChunkSize);
				
					$msg = [ "id" => (string) $listing->getId(), "provider" => (string) $this->getProvider()->getId() , "channel" => $channelId, "batch" => (string) $this->getBatch()->getId(), "jobsInBatch" => $jobsInBatch, "jobIndex" => $offset + ($index + 1 ) ];
					
					// CAN WE REFACTOR THIS SO THAT IT"S DONE IN ONE GO AT THE END? THIS TAKES FOREVER TO RUN>>>
					// TODO PERSIST< THEN FLUSH.
					$logger->info(sprintf("Sending Listing with ID of %s to Queue for Push Syncronization.",  (string) $listing->getId() ),  $msg );
					
					$routingKey = "lycan.provider.push.listing.rentivo";
					$this->container->get('lycan.rabbit.producer.push_listing')->publish(serialize($msg), $routingKey);
				}
			}
			
		};
	}
	
	public function doOutgoingMapping($json, Listing $listing){
		// For Rentivo, it is a single JSON. This will probabaly need refactoring when we
		// start pushing different requests to different providers. Likely there will be multi-batch transactions
		// or multi request that we need to take into account.
		$outgoing = new Processor(new OutgoingTransformer());
		
		$model  = $outgoing->process(
			$json,
			new SchemaContainer(),
			new OutgoingHydrator($this->getPassThroughProvider(), $listing )
		);
		return $model;
		
		
	}
	
	
	public function getProcessIncomingMappingClosure(){
		
		return function($data) {
			
			$incoming = new Processor(new IncomingTransformer());
			$schema   = $incoming->process(
				$data,
				new SchemaContainer(),
				new IncomingHydrator()
			);
			return new  SourceMappingResult($data, $schema);
		};
		
	}
	
	// Used to take some data and push to remote provider
	public function upsert(SchemaContainer $model, Listing $listing = null){
		$client = $this->getClient();
		
		$endpoint = "/api/r/properties/schemas";
		try {
			// AND EXISTS...
			if($listing && (string) $listing->getProviderListingId() !== "" ){
				$endpoint = sprintf( "/api/r/properties/schemas/%s", $listing->getProviderListingId() );
				$response = $client->getClient()->put( $endpoint,  [ 'json' => $model->toArray() ]);
			} else {
				$response = $client->getClient()->post( $endpoint,  [ 'json' => $model->toArray() ]);
			}
			// In order to upsert, we have to push to Rentivo and if we KNOW what the ID is on Rentivo, we should use that ID.
			// Otherwise we can just POST as a new property.
	
			$data = json_decode((string)$response->getBody(), true);
			
			if($response->getStatusCode() === 200){
				
				// This is the ID of the inserted property.
				$id = $data['data'][0]['id'];
				$logger = $this->container->get('app.logger.jobs');
				
				$logger->info("Successfully upserted Listing on Rentivo.", ["id"=> $id]);
				
				return $id;
			} else {
				// Failed to insert. We should log this..
				$logger = $this->container->get('app.logger.jobs');
				$data = json_decode((string) $response->getBody(), true);
				$logger->warning(sprintf("Error when trying to push rental to Rentivo. Returned a %s status code.", $response->getStatusCode()), [ "endpoint" => $endpoint, "input" => $model->toArray(), "output"=> $data ]);
				
				return null;
			}
			
		} catch(\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
			$logger = $this->container->get('app.logger.jobs');
			$opts = [ "input" => [ "id" => (string)$this->getProvider()->getId(), "name" => $this->getProvider()->getNickname(), "type" => $this->getProvider()->getProviderType()  ] ];
			$logger->critical("The provider returned an invalid identify exception. Please check credentials are correct." , $opts );
			return null;
			
		} catch (\Exception $e){
			$logger = $this->container->get('app.logger.jobs');
			$opts = [ "input" => [ "id" => (string)$this->getProvider()->getId(), "name" => $this->getProvider()->getNickname() ] ];
			$logger->critical("Unknown Exception thrown when pushing to Rentivo.", $opts );
			return null;
		}
		
		
	
	}
	
	
	/**
	 * @return null
	 */
	public function getBatch()
	{
		return $this->batch;
	}
	
	/**
	 * @param null $batch
	 */
	public function setBatch($batch)
	{
		$this->batch = $batch;
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