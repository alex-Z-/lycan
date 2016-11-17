<?php

namespace Lycan\Providers\RentivoBundle\API;

use Lycan\Providers\RentivoBundle\Entity\ProviderRentivoAuth;
use Lycan\Providers\CoreBundle\Entity\ProviderAuthBase;
use GuzzleHttp\Handler\CurlHandler;
use Somoza\Psr7\OAuth2Middleware;
use League\OAuth2\Client\Provider\GenericProvider;
use Lycan\Providers\CoreBundle\API\ClientInterface;
use React\Promise\Deferred;
class Client implements ClientInterface {
	
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
	
	protected function __construct()
	{
	}
	
	
	public function setContainer($container){
		$this->container = $container;
	}
	
	public function setAuthProvider(ProviderAuthBase  $auth){
		
		$stack = new \GuzzleHttp\HandlerStack();
		$stack->setHandler(new CurlHandler());
		$client = new \GuzzleHttp\Client(['handler' => $stack, 'base_uri' => 'https://www.rentivo.com' ]);
		
		// instantiate a provider, see league/oauth2-client docs
		$provider = new GenericProvider(
			[
				'clientId' => $auth->getClient(),
				'clientSecret' => $auth->getSecret(),
				'urlAuthorize' => 'https://www.rentivo.com/oauth2/auth',
				'urlAccessToken' => 'https://www.rentivo.com/oauth2/auth',
				'urlResourceOwnerDetails' => null,
			],
			[ 'httpClient' => $client ] // or don't pass it and let the oauth2-client create its own Guzzle client
		);
		
		// attach our oauth2 middleware
		$oauth2 = new OAuth2Middleware\Bearer($provider);
		$stack->push($oauth2);
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
		$response = $this->_fetchAllListings();
		$data = \GuzzleHttp\json_decode(  $response->getBody(), true );
		$deferred->resolve($data);
		return $deferred->promise();
	}
	
	private function _fetchAllListings(){
		$response = $this->getClient()->get('/api/r/properties');
		return $response;
	}
	
	public function getListingFull($id){
		
		$deferred = new Deferred();
		$response = $this->getClient()->get( sprintf('/api/public/properties/schemas/%s', $id ) );
		$result = json_decode ( (string) $response->getBody(), true );
		
		$data = $result['data'];
		$deferred->resolve($data);
		return $deferred->promise();
	}
	
	
	
	
	
	
	
	
	
}