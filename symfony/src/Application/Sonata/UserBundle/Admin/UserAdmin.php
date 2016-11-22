<?php

namespace Application\Sonata\UserBundle\Admin;

use Sonata\UserBundle\Admin\Entity\UserAdmin as BaseAdmin;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Admin\AdminInterface;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Show\ShowMapper;
class UserAdmin extends  BaseAdmin
{
	
	protected function configureFormFields(FormMapper $formMapper){
		parent::configureFormFields($formMapper);
		
		$p = $this->getSubject();
		
		//$em = $this->modelManager->getEntityManager($this->getSubject())->getRepository("Application\Sonata\UserBundle\Entity\User")->find(1);
		// dump($em->getOwnedProperties());die();
		// $query = $this->modelManager->getEntityManager($entity)->createQuery('SELECT s FROM MyCompany\MyProjectBundle\Entity\Seria s ORDER BY s.nameASC');
		
		/*
		$formMapper->remove('groups');
		
		$formMapper->tab('Security')
			->with('Groups')
			->add('groups', 'sonata_type_model', array(
				'required' => false,
				'expanded' => true,
				'multiple' => true,
			))
		->end()->end();
		*/
		$formMapper->tab("Notes")
		->with("Your Personal Notes")
			->add('notes', 'textarea',  array('required' => false));
		
	}
	
	
	protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
	{
		
		if (!$childAdmin && !in_array($action, array('edit'))) {
			return;
		}
		
		$admin = $this->isChild() ? $this->getaParent() : $this;
		
		// $id = $admin->getRequest()->get('id');
		$router = $this->getConfigurationPool()->getContainer()->get('router');
		
		
		if($admin->getSubject()->getId()) {
			
			$menu->addChild(
				$this->trans('View Properties', array(), 'SonataUserBundle'),
				array('uri' => $router->generate('admin_app_property_list',
					array(
						// 'id' => (string) $admin->getSubject()->getId(),
						'filter[owner][value]' => (string)  $admin->getSubject()->getId()
					)
				))
			);
		}
		
		
	}
}