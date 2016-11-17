<?php
namespace Lycan\Providers\CoreBundle\API;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
class ManagerFactory {
	protected $container;
	public function __construct()
	{
		
	}
	

	public function setContainer($container){
		$this->container = $container;
	}
	
	public function create($provider){
		try {
			
			$manager = $this->container->get(sprintf("lycan.provider.manager.%s", $provider));
			return $manager;
		} catch(ServiceNotFoundException $e){
			return null;
		}
	}
}