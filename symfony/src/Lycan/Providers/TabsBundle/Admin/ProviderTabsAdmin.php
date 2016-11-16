<?php

namespace Lycan\Providers\TabsBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Admin\AdminInterface;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Lycan\Providers\CoreBundle\Admin\ProviderAdmin;
class ProviderTabsAdmin extends ProviderAdmin
{
	
	const  ACCESS_ROLE_FOR_USERFIELD ="ROLE_SUPERADMIN";

	
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
			->with('Tabs Credentials')
				->add('baseUrl', 'url')
				->add('client', 'text', array('label' => 'Public Key'))
				->add('secret', 'text')
			->end()
			->end();
		
		
		
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
			->add('client', null, array('label' => 'Public Key'))
			->add('owner')
			->add('_action', 'actions', array(
				'actions' => array(
					'edit' => array(),
					'delete' => array(),
					'pull' => array(
						'template' => 'CoreBundle:CRUD:list__action_pull.html.twig'
					)
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