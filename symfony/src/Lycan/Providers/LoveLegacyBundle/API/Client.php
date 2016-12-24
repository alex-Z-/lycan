<?php

namespace Lycan\Providers\LoveLegacyBundle\API;

use Lycan\Providers\LoveLegacyBundle\API\Client\Middleware\HmacAuthMiddleware;
use Lycan\Providers\LoveLegacyBundle\API\Client\Middleware\Key as Key;
use Lycan\Providers\LoveLegacyBundle\Entity\ProviderLoveLegacyAuth;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Promise;
use React\Promise\Deferred;
class Client {
	
	private static $instance;
	private $client;
	private $container;
	private $auth;
	public static function getInstance()
	{
		if (null === static::$instance) {
			static::$instance = new static();
		}
		
		return static::$instance;
	}
	
	public function setContainer($container){
		$this->container = $container;
	}
	
	
	protected function __construct()
	{
	}
	
	public function setAuthProvider(ProviderLoveLegacyAuth  $auth){
		
		// A key consists of your UUID and a MIME base64 encoded shared secret.
		$key = new Key( $auth->getClient(), $auth->getSecret() );
		$this->auth = $auth;
		$options = ['headers' => []];
		// Provide your key, realm and optional signed headers.
		$middleware = new HmacAuthMiddleware($key, 'LoveLegacy', array_keys($options['headers']));
	
		// Register the middleware.
		$stack = HandlerStack::create();
		
		$stack->push($middleware);
		
		// Create a client.
		$client = new HttpClient([
			'base_uri' => $auth->getBaseUrl(),
			'handler' => $stack,
		]);
	
		$this->setClient($client);
		return $this;
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
	
	// Simple proxy.
	public function get(){
		
		return call_user_func_array([$this->getClient(), "get"], func_get_args());
		
	}
	
	public function fetchAllListings(){
		$deferred = new Deferred();
		
		// "url" => "http://di.api.carltonsoftware.co.uk/property?APIKEY=hydrant&orderBy=propname_asc&page=0&pageSize=5&hash=e8f4e66c4b80af4edd55e796aa198684ff97ef7821ad6796716e993bae93cbd3"
		$params = array(
			'pageSize' => 1000,
			'page'     => 0, // Starts at page 1.
			'orderBy'  => 'propname_asc',
		);
		
		$response = $this->getClient()->get( $this->auth->getBaseUrl() . '/property', [ 'query' => $params ]);
		$data     =  json_decode($response->getBody(), true);
		
		if(!isset($data['pageSize']) && !isset($data['pagesize'])){
			throw new \Exception("Provider does not return a valid data response. Missing pageSize");
		}
		$pageSizeKey = isset($data['pageSize']) ? "pageSize" : "pagesize";
		
		// First step, we need to know how many pages we are expected to pull from.
		// Get the number of results, and then divide by the pageSize.
		$results = (int) $data['totalResults'];
		$pageSize = (int) $data[$pageSizeKey];
		$totalPages = (int) round( ceil( ($results / $pageSize) ), 0);
		
		$promises = [];
		foreach(range(0, $totalPages) as $currentPage){
			$param = $params;
			$param['page'] = $currentPage;
			$promises[] = $this->getClient()->getAsync($this->auth->getBaseUrl() . "/property", [ 'query' => $param ]);
			
		}
		
		try {
			Promise\all($promises)
				->then(function (array $responses) use ($deferred) {
					$results = [];
					foreach ($responses as $index => $response) {
						
						$data = json_decode((string)$response->getBody(), true);
						if(isset($data['results'])) {
							$results = array_merge($results, $data['results']);
						}
						
					}
					
					$deferred->resolve($results);
				})
				->wait();
		} catch(\Exception $e){
			$deferred->reject($e);
		}
		
		return $deferred->promise();
	}
	
	public function ping(){
		// They don't have a ping. So just use this.
		try {
			$params = array(
				'pageSize' => 1,
				'page'     => 1, // Starts at page 1.
				'orderBy'  => 'propname_asc',
			);
			
			$response = $this->getClient()->get( $this->auth->getBaseUrl() . '/property', [ 'connect_timeout' => 8, 'query' => $params ]);
			$data     =  json_decode($response->getBody(), true);
			return $response;
		} catch(\Exception $e){
			throw $e;
		}
	}
	
	// If it's an array, just return.
	public function getListingFull($id){
		$deferred = new Deferred();
	
		// $property              = $this->makeRequest("GET", "/property/{$property_id}");
		// $property->description = $this->makeRequest("GET", "/property/{$property_id}/description");
		if(is_array($id)) {
		
			// We still need to fetch the availability for this rental!
			$response = $this->getClient()->get( $this->auth->getBaseUrl() . '/property/' . $id['id'] . "/availablebreaks" );
			$data     =  json_decode( (string) $response->getBody(), true);
			$id['pricing'] = $data;
			$deferred->resolve($id);
			return $deferred->promise();
		} else {
			
			// We need to fetch the rental and then the availability!
			
			throw new Exception("You cannot retrieve a single property for this provider");
		}
	}
	
	
	
	
	
	
	
	
	
	
}