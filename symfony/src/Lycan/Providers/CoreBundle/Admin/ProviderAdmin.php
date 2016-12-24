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
	
	
	protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
	{
		
		if (!$childAdmin && !in_array($action, array('edit'))) {
			return;
		}
		
		$admin = $this->isChild() ? $this->getParent() : $this;
		
		// $id = $admin->getRequest()->get('id');
		$router = $this->getConfigurationPool()->getContainer()->get('router');
		
		if($admin->getSubject()->getOwner()) {
			
			$menu->addChild(
				$this->trans('Edit Owner', array(), 'SonataUserBundle'),
				array('uri' => $router->generate('admin_sonata_user_user_edit', array('id' => $admin->getSubject()->getOwner()->getId() )))
			);
		}
		
		if ( $this->isGranted("ROLE_SUPERADMIN")  ){
			$menu->addChild(
				'Show Event Log',
				
				array('uri' => $router->generate('admin_providers_core_event_list',
					array(
						'provider_id' => $admin->getSubject()->getId(),
						'filter[provider][value]' => (string)  $admin->getSubject()->getId()
					)
				)));
		}
		
		
		// TODO - THIS COUNTS PROPERTIES AND LISTINGS... Which can easily be wrong!
		
		
		if($admin->getSubject()->getShouldPull()){
			$route = 'admin_app_property_list';
			$anchor = sprintf("View Properties (%d)", $admin->getSubject()
				->getProperties()
				->count());
		} else if($admin->getSubjecT()->getAllowPush()){
			$route = 'admin_app_listing_list';
			$anchor = sprintf("View Mapped Listings (%d)", $admin->getSubject()
				->getProperties()
				->count());
		}
		if(isset($route)) {
			$menu->addChild(
				$anchor,
				
				['uri' => $router->generate($route,
					[
						'id'                      => (string)$admin->getSubject()
							->getId(),
						'filter[provider][value]' => (string)$admin->getSubject()
							->getId()
					]
				)]);
		}
		
	}
	
	public function getBatchActions()
	{
		// retrieve the default batch actions (currently only delete)
		$actions = parent::getBatchActions();
		
		if (
			$this->hasRoute('edit') && $this->isGranted('EDIT') &&
			$this->hasRoute('delete') && $this->isGranted('DELETE')
		) {
			$actions['pull'] = array(
				'label' => 'Start - Pull Providers',
				'ask_confirmation' => true
			);
				
			$actions['pullStop'] = array(
				'label' => 'Stop - Pulling Providers',
				'ask_confirmation' => true
			);
			
			$actions['validateCredentials'] = array(
				'label' => 'Validate Credentials',
				'ask_confirmation' => false
			);
			
		}
		
		return $actions;
	}
	
	
	public function getNewInstance()
	{
		$instance = parent::getNewInstance();
		$instance->setPassOnCredentials(true);
		$instance->setAllowPush(true);
		
		return $instance;
	}
	
	protected function configureRoutes(RouteCollection $collection)
	{
		
		
		$collection->add('pull', $this->getRouterIdParameter().'/pull');
		$collection->add('pullStop', $this->getRouterIdParameter().'/pull-stop');
		// to remove a single route
		$collection->remove('acl');
		// OR remove all route except named ones
		// $collection->clearExcept(array('list', 'show'));
		
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
	
	
	public function preUpdate($object)
	{
		// If we haven't validated the credentials we should attempt to do it now.
		$providerKey = strtolower( $object->getProviderName() );
		$this->container = $this->getConfigurationPool()->getContainer();
		$client = $this->container->get('lycan.provider.api.factory')->create($providerKey, $object);
		try {
			if(method_exists($client, 'ping')) {
				$ponged = $client->ping();
				
				if ($ponged->getStatusCode() === 200) {
					$object->setIsValidCredentials(true);
				} else {
					$this->getRequest()
						->getSession()
						->getFlashBag()
						->add("error", "The current credentials are invalid.");
					$object->setIsValidCredentials(false);
				}
			}
		} catch(\Exception $e){
			$object->setIsValidCredentials(false);
			$this->getRequest()->getSession()->getFlashBag()->add("error", "Failed with message: " . $e->getMessage() );
		}
			
		
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
						
					),
					'delete' => array(),
				)
			));
		
		
		
		
	}
	
	
	
	
}