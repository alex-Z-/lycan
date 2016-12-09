<?php

namespace Lycan\Providers\TabsBundle\API;

use Lycan\Providers\TabsBundle\API\Client\Middleware\HmacAuthMiddleware;
use Lycan\Providers\TabsBundle\API\Client\Middleware\Key as Key;
use Lycan\Providers\TabsBundle\Entity\ProviderTabsAuth;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Promise;
use React\Promise\Deferred;
class Client {
	
	private static $instance;
	private $client;
	private $container;
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
	
	public function setAuthProvider(ProviderTabsAuth  $auth){
		
		// A key consists of your UUID and a MIME base64 encoded shared secret.
		$key = new Key( $auth->getClient(), $auth->getSecret() );
		$options = ['headers' => []];
		// Provide your key, realm and optional signed headers.
		$middleware = new HmacAuthMiddleware($key, 'Tabs', array_keys($options['headers']));
		
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
		
		try {
			$response = $this->_fetchAllListings();
			$data     = \GuzzleHttp\json_decode($response->getBody(), true);
			$deferred->resolve($data);
		} catch (\Exception $e) {
			$deferred->reject($e);
		}
		return $deferred->promise();
	}
	
	private function _fetchAllListings(){
		// "url" => "http://di.api.carltonsoftware.co.uk/property?APIKEY=hydrant&orderBy=propname_asc&page=0&pageSize=5&hash=e8f4e66c4b80af4edd55e796aa198684ff97ef7821ad6796716e993bae93cbd3"
		$params = array(
			'pageSize' => 1000,
			'page'     => 0,
			'orderBy'  => 'propname_asc',
		);
		
		$response = $this->getClient()->get('/property', [ 'query' => $params ]);
		return $response;
	}
	
	public function ping(){
		// They don't have a ping. So just use this.
		try {
			$response = $this->getClient()
				->get('/api/setting', []);
			return $response;
		} catch(\Exception $e){
			throw $e;
		}
	}
	
	public function getListingFull($id){
		$deferred = new Deferred();
		
		// $property              = $this->makeRequest("GET", "/property/{$property_id}");
		// $property->description = $this->makeRequest("GET", "/property/{$property_id}/description");
		$params = [ 'pageSize' => 0];
		
		$promises = [
			"_" => $this->getClient()->getAsync("/property/$id", [ 'query' => $params ]),
			"description" => $this->getClient()->getAsync("/property/$id/description", [ 'query' => $params ]),
			"availablebreaks" => $this->getClient()->getAsync("/property/$id/availablebreaks", [ 'query' => $params ]),
			"calendar" => $this->getClient()->getAsync("/property/$id/calendar", [ 'query' => $params ]),
			
			
		];
		Promise\all($promises)->then( function( array $responses)  use ($deferred)  {
		
			foreach( $responses as $index => $response){
				
				$data = json_decode((string)$response->getBody(), true);
				if(!isset($results)) {
					$results = $data;
				} else {
					$results[$index] = $data;
				}
			}
		
			$deferred->resolve($results);
		})->wait();
		
		return $deferred->promise();
	}
	
	
	
	
	
	
	
	
	
	
}