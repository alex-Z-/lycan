<?php
namespace AppBundle\Menu;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
 
class MenuBrandBuilder extends ContainerAware
{
	public function createMenu(FactoryInterface $factory, array $options)
	{
		
		$router = $this->container->get("templating.helper.router");
		$route =  "admin_app_brand_create";
		$uri = $router->generate( $route );
		
		$menu = $factory->createItem('root');
		$menu->setChildrenAttribute('class', 'nav pull-right');
		$menu->addChild('Create a new Brand', array('uri' => $uri));
		
		return $menu;
	}
}