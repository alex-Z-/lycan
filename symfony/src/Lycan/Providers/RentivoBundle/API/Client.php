<?php

namespace Lycan\Providers\RentivoBundle\API;

use Lycan\Providers\RentivoBundle\Entity\ProviderRentivoAuth;
use GuzzleHttp\Handler\CurlHandler;
use Somoza\Psr7\OAuth2Middleware;
use League\OAuth2\Client\Provider\GenericProvider;
class Client {
	
	private static $instance;
	private $client;
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
	
	public function setAuthProvider(ProviderRentivoAuth  $auth){
		
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
	
	public function fetchAllProperties(){
		$response = $this->_fetchAllProperties();
		return \GuzzleHttp\json_decode(  $response->getBody(), true );
	}
	
	private function _fetchAllProperties(){
		$response = $this->getClient()->get('/api/r/properties');
		return $response;
	}
	
	public function getPropertyFull($id){
		
		$response = $this->getClient()->get( sprintf('/api/public/properties/schemas/%s', $id ) );
		$result = json_decode ( (string) $response->getBody(), true );
		$data = $result['data'];
		return $data;
	}
	
	
	
	
	
	
	
	
}