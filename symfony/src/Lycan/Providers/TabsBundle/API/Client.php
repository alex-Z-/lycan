<?php

namespace Lycan\Providers\TabsBundle\API;

use Lycan\Providers\TabsBundle\API\Client\Middleware\HmacAuthMiddleware;
use Lycan\Providers\TabsBundle\API\Client\Middleware\Key as Key;
use Lycan\Providers\TabsBundle\Entity\ProviderTabsAuth;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client as HttpClient;

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
	
	public function fetchAllProperties(){
		$response = $this->_fetchAllProperties();
		return \GuzzleHttp\json_decode(  $response->getBody(), true );
	}
	
	private function _fetchAllProperties(){
		// "url" => "http://di.api.carltonsoftware.co.uk/property?APIKEY=hydrant&orderBy=propname_asc&page=0&pageSize=5&hash=e8f4e66c4b80af4edd55e796aa198684ff97ef7821ad6796716e993bae93cbd3"
		$params = array(
			'pageSize' => 500,
			'page'     => 0,
			'orderBy'  => 'propname_asc',
		);
		
		$response = $this->getClient()->get('/property', [ 'query' => $params ]);
		return $response;
	}
	
	public function getPropertyFull($id){
		
		$response = $this->getClient()->get( sprintf('/api/public/properties/schemas/%s', $id ) );
		$result = json_decode ( (string) $response->getBody(), true );
		$data = $result['data'];
		return $data;
	}
	
	
	
	
	
	
	
	
}