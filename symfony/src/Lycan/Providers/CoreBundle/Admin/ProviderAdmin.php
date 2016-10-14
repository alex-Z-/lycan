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
	
	protected function configureRoutes(\Sonata\AdminBundle\Route\RouteCollection $collection)
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