<?php

namespace Lycan\Providers\SupercontrolBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Admin\AdminInterface;
use Knp\Menu\ItemInterface as MenuItemInterface;

class ProviderSupercontrolAdmin extends AbstractAdmin
{
	
	const  ACCESS_ROLE_FOR_USERFIELD ="ROLE_SUPERADMIN";

	
	protected function configureRoutes(RouteCollection $collection)
	{
		
	
		// to remove a single route
		$collection->remove('acl');
		// OR remove all route except named ones
		// $collection->clearExcept(array('list', 'show'));
	
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
		
		
	}
	
	public function generateObjectUrl($name, $object, array $parameters = array(), $absolute = false)
	{
		
		// THIS DOESNT DO ANYTHING. NO IDEA HOW TO GENERATE OBJECT URL FOR THAT...
		if ('edit_owner' == $name) {
			
			return $this->getRouteGenerator()->generate('admin_sonata_user_user_edit', [
				'id' => $this->getUrlsafeIdentifier($object),
			], $absolute );
		}
		$parameters['id'] = $this->getUrlsafeIdentifier($object);
		return $this->generateUrl($name, $parameters, $absolute);
	}
	
	protected function configureFormFields(FormMapper $formMapper)
	{
		$formMapper
			->add('nickname', 'text')
			->end()
			->with('Supercontrol Credentials')
				->add('baseUrl', 'url')
				->add('client', 'text', [ 'label' => 'Site ID' ])
				->add('secret', 'text')
			->end()
			->end();
		
		// We don't want to let properties be transfered until we understand more of the implications.
		if ($this->isGranted(  SELF::ACCESS_ROLE_FOR_USERFIELD ) ) {
			
			$formMapper->with('Owner')
				->add('owner', 'sonata_type_model', array(
					'required' => false,
					'expanded' => false,
					'btn_add' => false,
					'multiple' => false,
				))
				->end();
			
		}
		
	}
	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper->add('baseUrl')
			->add('client');

	}
	
	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper->addIdentifier('nickname', null, array(
				'route' => array(
					'name' => 'edit'
				)
			))
			->add('baseUrl')
			->add('client', null, array('label' => 'Site ID'))
			->add('_action', 'actions', array(
				'actions' => array(
					'edit' => array(),
					'delete' => array(),
				)
			));
		
		
	}
	
	public function createQuery($context = 'list')
	{
		$query = parent::createQuery($context);
		
		if ( !$this->isGranted("ROLE_SUPERADMIN") ){
			$owner = $this->getConfigurationPool()->getContainer()->get('security.context')->getToken()->getUser();
			$query->andWhere('o.owner = :owner')
			->setParameter('owner', $owner);
		}
		return $query;
	}
	
	public function prePersist($property)
	{
		if($property->getOwner() === null ){
			$owner = $this->getConfigurationPool()->getContainer()->get('security.context')->getToken()->getUser();
			$property->setOwner( $owner );
		}
		
	}
	
	
}