<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use AppBundle\Exception\NoBrandFoundException;
use Knp\Menu\FactoryInterface as MenuFactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\CoreBundle\Validator\ErrorElement as ErrorElement;
class UserBrandRegistryAdmin extends BaseAdmin
{
	protected $parentAssociationMapping = 'brand';
	public $hasNoBrands  = false;
	public function getNewInstance()
	{
		$instance = parent::getNewInstance();
		
		// $instance->setRef($this->getConfigurationPool()->getContainer()->get('doctrine')->getRepository($this->getClass())->findNextRef());
		
		if ($this->getRequest()->get('code') == 'admin.brand' && ($this->getRequest()->get('objectId') || $this->getSubject())) {
			$brand_id = $this->getSubject() ? $this->getSubject()->getBrand()->getId() : $this->getRequest()->get('objectId');
			// Set is child form...
			$instance->setBrand($this->getConfigurationPool()->getContainer()->get('doctrine')->getRepository('AppBundle\Entity\Brand')->find($brand_id));
		}
		
		
		return $instance;
	}
	
	protected function configureFormFields(FormMapper $formMapper)
	{
		
		$owner = $this->getConfigurationPool()->getContainer()->get('security.context')->getToken()->getUser();
		$em =  $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
		// define group zoning
		$formMapper->tab('Brand Members')->with('Members')->end();
		
	
		if ($this->hasParentFieldDescription()) {
			$formMapper->add('brand', 'sonata_type_model_hidden');
		} else {
			
			if ($this->getSubject() && $this->getSubject()->getBrand()) {
				$owner = $this->getSubject()->getBrand()->getOwner()->getId();
			}
			// We only want to be able to create IF you own the brand....
			$query = $query = $em->createQueryBuilder("b")
				->select("b")
				->from("AppBundle\Entity\Brand", "b")
				->where("b.owner = :owner")
				->setParameter("owner", $owner);
			
			if ( !$this->isGranted("ROLE_SUPERADMIN") ||  !$this->isGranted('MASTER') ){
				$this->hasNoBrands = (bool) ($query->getQuery()->getResult() === []);
			}
			
			
			if( $this->hasNoBrands ){
				
				$formMapper
					->with('Members' , [
						'box_class' => 'box box-danger',
						'description' => "You have not created any brands yet."  .
							"<br><br><b>How to fix this?</b><br />" .
							"Simply create a brand and start adding your properties."
					])->end();
			}
			
			$formMapper->with('Members')->add('brand', 'sonata_type_model',
				[
					'btn_add' => false,
					'required' => true,
					'query' => $query
				]
			);
			
			
		}
		

		// Since ONLY the owner of the brand can add new members, we will just assume that the members field
		// will only be shown. So in which case, we are being lazy, and not getting the owner of the BRAND,
		// but instead just the logged in token.
		// WRONG WRONG WRONG.. As above...
		// This means when an admin views a brand by another user.. he get's the wrong view of potential brands...
		
		$query = $query = $em->createQueryBuilder("b")
			->select("u")
			->from("Application\Sonata\UserBundle\Entity\User", "u")
			->where("u.id <> :owner and u.username <> 'admin'")
			->setParameter("owner", $owner);
		
		$formMapper->add('member', 'sonata_type_model',
			[
				'required' => true,
				'expanded' => false,
				'btn_add' => false,
				'multiple' => false,
				'query' => $query
			]
		)->end();
		
		
	}
	
	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper->add('brand')
			->add('member');
	}
	
	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper->addIdentifier('brand', null, array(
			'route' => array(
				'name' => 'edit'
			)
		))
			->add('member')
			->add('_action', 'actions', array(
				'actions' => array(
					// 'edit' => array(),
					'delete' => array(),
				)
			));
		
	}
	
	// add this method
	public function validate(ErrorElement $errorElement, $object)
	{
		$errorElement
			->with('brand')
			->assertNotNull()
			->end();
	}
	
	
	public function createQuery($context = 'list')
	{
		$query = parent::createQuery($context);
		if (!$this->isGranted("ROLE_SUPERADMIN") || !$this->isGranted('MASTER')) {
			$owner = $this->getConfigurationPool()->getContainer()->get('security.context')->getToken()->getUser();
			$query->leftjoin("o.brand", "b");
			$query->orWhere('b.owner = :owner')
				->setParameter('owner', $owner);
		}
		
		return $query;
	}
	
	
}