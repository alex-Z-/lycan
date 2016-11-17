<?php
namespace Lycan\Providers\CoreBundle\API;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
class ClientFactory {
	protected $container;
	public function __construct()
	{
		
	}
	

	public function setContainer($container){
		$this->container = $container;
	}
	
	public function create($provider, $auth){
		try {
			$client = $this->container->get(sprintf("lycan.provider.api.%s", $provider));
			$client->setAuthProvider($auth);
			return $client;
		} catch(ServiceNotFoundException $e){
			return null;
		}
	}
}