<?php

namespace Lycan\Providers\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Admin\AdminInterface;
use Knp\Menu\ItemInterface as MenuItemInterface;
use AppBundle\Admin\BaseAdmin as BaseAdmin;

class ProviderPolicyAdmin extends BaseAdmin
{
	
	
	const  ACCESS_ROLE_FOR_USERFIELD ="MASTER";
	
	
	protected $container;
	public function setContainer($container){
		$this->container = $container;
	}
	
	public function getNewInstance()
	{
		$instance = parent::getNewInstance();
		
		// $instance->setRef($this->getConfigurationPool()->getContainer()->get('doctrine')->getRepository($this->getClass())->findNextRef());
		
		if ( strpos( $this->getRequest()->get('code'), 'admin.lycan.provider.') !== FALSE && ($this->getRequest()->get('objectId') || $this->getSubject())){
			$provider_id = $this->getSubject() ? $this->getSubject()->getProvider()->getId() : $this->getRequest()->get('objectId');
			
			// Set is child form...
			$instance->setProvider($this->getConfigurationPool()->getContainer()->get('doctrine')->getRepository('CoreBundle:ProviderAuthBase')->find($provider_id));
		
		}
		
		
		return $instance;
	}
	
	
	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper->addIdentifier('descriptiveName', null, array(
				'route' => array(
					'name' => 'edit'
				)
			))
			->add('createdAt');
		
		$listMapper->add('_action', 'actions', array(
			'actions' => array(
				'edit' => array(),
				'delete' => array(),
			)));
		
		
		
	}
	
	protected function configureFormFields(FormMapper $formMapper)
	{
		
		// define group zoning
		$formMapper
			->tab('Policy')
				->with('Policy', array('class' => 'col-md-7'))->end()
			->end()		;
		
		
		if ($this->hasParentFieldDescription()) {
			
			$formMapper
				->tab('Policy')
				->add('provider', 'sonata_type_model_hidden')
				->add('policy')
				->end();
		
		} else {
			throw new \Exception ("Cannot create a Policy Relationship outside of Parent Admin.");
		}
		
		
		
		
	}
	
	
	
	
}