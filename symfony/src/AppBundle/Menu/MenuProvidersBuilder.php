<?php
namespace AppBundle\Menu;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
 
class MenuProvidersBuilder extends ContainerAware
{
	public function createMenu(FactoryInterface $factory, array $options)
	{
		
		
		$providers = $this->container->getParameter("lycan.core.providers");
		$router = $this->container->get("templating.helper.router");
		$routes = [];
		$uris = [];
		foreach($providers as $provider){
			$route = sprintf( "admin_providers_%s_provider%sauth_create", strtolower($provider['name']), strtolower($provider['name']));
			$routes[] = $routes;
			$uris[$provider['name']] = $router->generate( $route );
		}
		
		
		$menu = $factory->createItem('root');
		$menu->setChildrenAttribute('class', 'nav pull-right');
		$menu->addChild('Create a new Channel Provider')->setAttribute('dropdown', true);
		foreach($uris as $key=> $uri ){
			$menu['Create a new Channel Provider']->addChild($key, array('uri' => $uri));
		}
		
		
		return $menu;
	}
}