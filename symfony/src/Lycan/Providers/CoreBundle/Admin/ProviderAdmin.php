<?php

namespace Lycan\Providers\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Admin\AdminInterface;
use Knp\Menu\ItemInterface as MenuItemInterface;
use AppBundle\Admin\BaseAdmin as BaseAdmin;

class ProviderAdmin extends BaseAdmin
{
	
	const  ACCESS_ROLE_FOR_USERFIELD ="ROLE_SUPERADMIN";
	
	protected $container;
	public function setContainer($container){
		$this->container = $container;
	}
	
	protected function configureRoutes(RouteCollection $collection)
	{
		
		$collection->add('go', $this->getRouterIdParameter().'/go');
		$collection->add('pull', $this->getRouterIdParameter().'/pull');
		$collection->add('pullStop', $this->getRouterIdParameter().'/pull-stop');
		// to remove a single route
		$collection->remove('acl');
		// OR remove all route except named ones
		// $collection->clearExcept(array('list', 'show'));
		
	}
	
	protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
	{
		
		if (!$childAdmin && !in_array($action, array('edit', 'show'))) {
			return;
		}
		
		
		$admin = $this->isChild() ? $this->getParent() : $this;
		
		$router = $this->getConfigurationPool()->getContainer()->get('router');
		
		if($childAdmin && $childAdmin->getBaseCodeRoute() === "admin.lycan.providers|admin.lycan.batch_executions" && $childAdmin->getSubject()){
			$menu->addChild(
				$this->trans('View Events in Job', array(), 'SonataUserBundle'),
				array('uri' => $router->generate('admin_providers_core_batchexecutions_event_list',
					array(
						'id' => (string) $childAdmin->getSubject()->getId(),
						// 'filter[brands][value]' => (string)  $admin->getSubject()->getId()
					)
				))
			);
		}
		
	}
	
	
	public function getPersistentParameters()
	{
		if (!$this->getRequest()) {
			return array();
		}
		return array(
			'subclass' => $this->getRequest()->get('subclass'),
		);
		
	}
	

	protected function configureFormFields(FormMapper $formMapper)
	{
		
		// This is super hacky.. but not sure on the best way.
		// We assume that all models/entities will share the convention of the bundle naming.
		
		if($this->getActiveSubClass() || $this->getSubject() !== 'Lycan\Providers\CoreBundle\Entity\ProviderAuthBase' ){
		
			$providers = $this->container->getParameter("lycan.core.providers");
			foreach($providers as $class){
				
				$subjectName = get_class($this->getSubject());
				$suspectedBundle = preg_match('/Lycan.Providers.(.*)Bundle.*/i', $subjectName, $matches );
			
				if($class['name'] === $this->getActiveSubclassCode() || $class['name'] === 	$matches[1] ){
					$subclassName = $class['adminClass'];
					$sub = new $subclassName($this->code, $this->getClass(), $this->baseControllerName);
				
					$sub->setSecurityHandler( $this->getSecurityHandler() );
					$sub->configureFormFields($formMapper);
				}
			}
			
		}
		
		
		return $formMapper;
		
	}
	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
	
	}
	
	
	protected function configureListFields(ListMapper $listMapper)
	{
		
		
		$listMapper->addIdentifier('nickname', null, array(
				'route' => array(
					'name' => 'edit'
				)
			))
			->add('providerName')
			->add('client')
			->add('owner')
			->add('_action', 'actions', array(
				'actions' => array(
					'edit' => array(
						'id' => 3
					),
					'delete' => array(),
				)
			));
		
		
		
		
	}
	
	
	
	
}