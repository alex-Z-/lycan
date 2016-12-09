<?php

namespace Lycan\Providers\SupercontrolBundle\API;


use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Promise;
use Lycan\Providers\SupercontrolBundle\Entity\ProviderSupercontrolAuth;
use React\Promise\Deferred;
use Lycan\Providers\SupercontrolBundle\API\Middleware\AuthMiddleware;
use Lycan\Providers\SupercontrolBundle\API\Convert\XmlToArray as Util;
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
	
	public function setAuthProvider(ProviderSupercontrolAuth  $auth){
		
		// A key consists of your UUID and a MIME base64 encoded shared secret.
		
		$options = ['headers' => []];
		// Provide your key, realm and optional signed headers.
		$middleware = new AuthMiddleware( $auth );
		
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
			$body     = simplexml_load_string($response->getBody());
			$data     = Util::xmlToArray($body);
			
			if (isset($data['scAPI']['error']) || !isset($data['scAPI'])) {
				$msg = $data['scAPI']['error']['status'] . " : " . $data['scAPI']['error']['msg'];
				$deferred->reject($msg);
			} else if (isset($data['scAPI']['GetProperties']['property'])) {
				$listings = [];
				foreach ($data['scAPI']['GetProperties']['property'] as $listing) {
					if ($listing['@enabled'] === "yes") {
						$listings[] = $listing;
					}
				}
				$deferred->resolve($listings);
			}
		} catch (Exception $e) {
			$deferred->reject($e);
		}
		return $deferred->promise();
	}
	
	private function _fetchAllListings(){
		// "url" => "http://di.api.carltonsoftware.co.uk/property?APIKEY=hydrant&orderBy=propname_asc&page=0&pageSize=5&hash=e8f4e66c4b80af4edd55e796aa198684ff97ef7821ad6796716e993bae93cbd3"
	
		$sc =  "/api/endpoint/v1/GetProperties";
		// $rb = "https://requestb.in/18gc5r01";
		$response = $this->getClient()->post( $sc, ['body' =>  'EMPTY']);
	
		return $response;
	}
	
	public function getListingFull($id){
		$deferred = new Deferred();
		
	
		// $property              = $this->makeRequest("GET", "/property/{$property_id}");
		// $property->description = $this->makeRequest("GET", "/property/{$property_id}/description");
		
		$body = sprintf("<propertyID>%s</propertyID>", $id);
		$promises = [
			"_" => $this->getClient()->postAsync("/api/endpoint/v1/GetProperty", [ 'body' => $body ]),
			"images" => $this->getClient()->postAsync("/api/endpoint/v1/GetPropertyImages", [ 'body' => '<Property><PropertyID>'. $id .'</PropertyID></Property>' ]),
			"availability" => $this->getClient()->postAsync("/api/endpoint/v1/GetAvailability", [ 'body' => '<Property><PropertyID>'. $id .'</PropertyID></Property>' ]),
			"rates"	=> $this->getClient()->postAsync("/api/endpoint/v1/GetRatesAll", [ 'body' => '<Property><PropertyID>'. $id .'</PropertyID></Property>' ] )
		];
		
		Promise\all($promises)->then( function( array $responses)  use ($deferred)  {
					
			foreach( $responses as $index => $response){
				
				$body = simplexml_load_string($response->getBody());
				$data = Util::xmlToArray( $body, ["alwaysArray" => ["ImageCollection", "varcatitems", "BookedStay", "varcatitem", "varcat"] ] );
				
				if($index === "_"){
					if(isset($data['scAPI']['GetProperty']['property'])){
						$results = $data['scAPI']['GetProperty']['property'];
					} elseif(isset($data['scAPI']['GetProperty']['error'])) {
						$deferred->reject($data['scAPI']['GetProperty']['error']);
						break;
					} else {
						$deferred->reject("Missing Property Data from Request");
						break;
					}
				} else {
					$results[$index] = current($data['scAPI']);
				}
			}
			if(isset($results)) {
				$deferred->resolve($results);
			}
		})->wait();
		
		return $deferred->promise();
	}
	
	
	
	
	
	
	
	
	
	
}