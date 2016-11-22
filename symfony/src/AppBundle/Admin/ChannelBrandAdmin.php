<?php

namespace AppBundle\Admin;

use AppBundle\AppBundle;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use AppBundle\Exception\NoBrandFoundException;
use Knp\Menu\FactoryInterface as MenuFactoryInterface;
use Knp\Menu\ItemInterface;
use Sonata\CoreBundle\Validator\ErrorElement as ErrorElement;
class ChannelBrandAdmin extends BaseAdmin
{
	public $hasNoProviders = false;
	public $hasNoBrands  = false;
	protected $parentAssociationMapping = 'brand';
	protected $baseRoutePattern = 'channel';
	
	public function getNewInstance()
	{
		$instance = parent::getNewInstance();
		
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
		$em = $this->modelManager->getEntityManager('AppBundle:Brand');
		
		// We only want to be able to create IF you own the brand....
		$brandQuery = $query = $em->createQueryBuilder("b")
			->select("b")
			->from("AppBundle\Entity\Brand", "b")
			->where("b.owner = :owner")
			->setParameter("owner", $owner);
		
		// Only get your Providers...
		$providerQuery = $query = $em->createQueryBuilder("b")
			->select("b")
			->from("Lycan\Providers\CoreBundle\Entity\ProviderAuthBase", "b")
			->where('b.allowPush = 1');
		
		$brandOpts = [
			'btn_add' => false,
			'required' => true
		];
		
		$providerOpts = [
			'btn_add' => false,
			'required' => true,
			'label' => "Channel Connection",
			'query' =>  $providerQuery,
			'help' => 'Not seeing all of your channels? Make sure you have enabled the channel to be used as a downstream push channel.',
			'group_by' => function($val, $key, $index) {
				if( preg_match( "/(not active)/", $key )){
					return 'Channel Pushing Disabled';
				}else if ( strpos(trim($key), "Unattached") === 0 ) {
					return 'Not In Use';
				} else {
					return 'Already Active';
				}
			}
		];
		
		if ( !$this->isGranted("ROLE_SUPERADMIN") ||  !$this->isGranted('MASTER') ){
			$providerQuery
				->andWhere("b.owner = :owner")
				->setParameter("owner", $owner);
			$brandOpts['query'] = $brandQuery;
			$providerOpts['query'] = $providerQuery;
			$this->hasNoProviders = (bool) ($providerQuery->getQuery()->getResult() === []);
			$this->hasNoBrands = (bool) ($brandQuery->getQuery()->getResult() === []);
		}
		
		
		
		// define group zoning
		$formMapper
			->tab('Your Channel Bridge')
				->with('Brand', array('class' => 'col-md-6'))->end()
				->with('Push to Channel', array('class' => 'col-md-6'))->end()
			->end();
		
		// This means that we are a SUB FORM! That means we don't want to let them changethe brand...
		if ($this->hasParentFieldDescription()) {
			$formMapper
				->tab('Your Channel Bridge')
					->with('Brand')
						->add('brand', 'sonata_type_model_hidden')
					->end()
				->end();
		
		} else {
			
			// define group zoning
			$formMapper
				->tab('Your Channel Bridge')
					->with('Brand')
						->add('brand', 'sonata_type_model',	$brandOpts )
					->end()
				->end();
		}
		
		// If empty...
		if( $this->hasNoProviders ){
			
			$formMapper
				->tab('Your Channel Bridge')
					->with('Push to Channel' , [
						'box_class' => 'box box-danger',
						'description' => "Usually you would pick the channel that you want to push your Brand rentals to, but by the looks of things, you haven't added any credentials to any channel yet."  .
							"<br><br><b>How to fix this?</b><br />" .
							"Simply create a provider and add your credentials."])
					->end()
				->end();
		}
		
		if( $this->hasNoBrands ){
			$formMapper
				->tab('Your Channel Bridge')
					->with('Brand' , [
						'box_class' => 'box box-danger',
						'description' => "You have not created any brands yet."  .
							"<br><br><b>How to fix this?</b><br />" .
							"Simply create a brand and start adding your properties."])
					->end()
				->end();
		}
		
		
		// define group zoning
		$formMapper
			->tab('Your Channel Bridge')
				->with('Push to Channel')
					->add('provider', 'sonata_type_model', $providerOpts	)
				->end()
			->end();
		
		$subject = $this->getSubject();
		if (!$this->hasParentFieldDescription() && $subject->getId() !== null ) {
			$formMapper->get('brand')->setDisabled( true );
			$formMapper->get('provider')->setDisabled( true );
			// $formMapper->get('provider')->setAttribute("sonata_help", "IF YOU SEE THIS TELL ME");
			
		}
		
	}
	
	// add this method
	public function validate(ErrorElement $errorElement, $object)
	{
		$errorElement
			->with('provider')
			->assertNotNull()
			->end();
	}
	
	
	public function getFormTheme()
	{
		
		return array_merge(
			parent::getFormTheme(),
			array('AppBundle:Admin/ChannelBrandAdmin:admin.theme.html.twig')
		);
	}
	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		
	}
	

	
	protected function configureListFields(ListMapper $listMapper)
	{
		
		$listMapper->addIdentifier('id')
			->add('brand')
			->add('provider.providerName', null, [ 'label' => 'Channel'])
			->add('provider', null, [ 'label' => 'Credentials Used',  'associated_property' => 'typeAndName' ])
			->add('_action', 'actions', array(
				'actions' => array(
					'edit' => array(),
					'delete' => array(),
				)
			));
		
	}
	
	
	
	
}