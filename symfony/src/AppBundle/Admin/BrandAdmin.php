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
class BrandAdmin extends BaseAdmin
{
	
	const  ACCESS_ROLE_FOR_USERFIELD ="ROLE_SUPERADMIN";
	public $hasNoProviders = false;
	// protected $baseRouteName = 'admin_brandz';
	
	
	protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
	{
		
		if (!$childAdmin && !in_array($action, array('edit'))) {
			return;
		}
	
		$admin = $this->isChild() ? $this->getParent() : $this;
		$router = $this->getConfigurationPool()->getContainer()->get('router');
		if($admin->getSubject()->getId()) {
			
			$menu->addChild(
				$this->trans('View Brand Properties', array(), 'SonataUserBundle'),
				array('uri' => $router->generate('admin_app_brand_property_list',
					array(
						'id' => (string) $admin->getSubject()->getId(),
						// 'filter[brands][value]' => (string)  $admin->getSubject()->getId()
					)
				))
			);
		
			$menu->addChild('View Brand Channels', array('uri' => $router->generate('admin_app_brand_channelbrand_list', array( 'id' => (string) $admin->getSubject()->getId() 	))) );
		}
	
		
		
		
	}
	
	
	protected function configureFormFields(FormMapper $formMapper)
	{
		
		
		$em =  $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
		$owner = $this->getConfigurationPool()->getContainer()->get('security.context')->getToken()->getUser();
		// Set up Queries
		$query = $em->createQueryBuilder("b")
			->select("u")
			->from("Application\Sonata\UserBundle\Entity\User", "u")
			->where("u.id <> :owner and u.username <> 'admin'")
			->setParameter("owner", $owner->getId() );
		
		// Only get your Providers...
		$providerQuery = $query = $em->createQueryBuilder("b")
			->select("b")
			->from("Lycan\Providers\CoreBundle\Entity\ProviderAuthBase", "b")
			->where("b.owner = :owner")
			->setParameter("owner", $owner);
		
		if ( !$this->isGranted("ROLE_SUPERADMIN") ||  !$this->isGranted('MASTER') ){
			$this->hasNoProviders = (bool) ($providerQuery->getQuery()->getResult() === []);
		}
		
		// define group zoning
		$formMapper
			->tab('Brand')
				->with('Details', array('class' => 'col-md-7'))->end()
				->with('Ownership', array('class' => 'col-md-5'))->end()
			->end()
			->tab('Members')
				->with('Members', array('class' => 'col-md-5'))->end()
			->end()
			->tab('Settings')->end()
			->tab('Brand')
				->with("Details")
					->add('brandName', 'text')
					->add('descriptiveName', 'text', ['label' => 'Brand Description'])
				->end()
			->end()
			->tab('Members')
				->with("Members", [
							'box_class' => 'box box-warning',
							'description' => "The members field allows you to add other users to add their rentals to your brand. For example, if you allow other users to push properties to your brand you can add them here."])
					->add('members', 'sonata_type_collection',
						array(
							'help' => 'Add Members to Any Brand',
							'required' => false,
							'by_reference' => false,
							'btn_add' => 'Add Member to Brand',
							'type_options' => array(
								// Prevents the "Delete" option from being displayed
								'delete' => true,
								
								
							)
							// 'label' => false,
							// 'sonata_help' => "Add Users to this brand",
							// 'sonata_field_description' => "Add Users to this brand",
							// 'query' => $query,
							// 'btn_add' => false, 'by_reference' => false, 'expanded' => false, 'multiple' => true, 'label' => 'Users'
						),
						array(
							'edit' => 'inline',
							'inline' => 'table'
						)
					)
				->end()
			->end();
		
		// We don't want to let properties be transfered until we understand more of the implications.
		if ($this->isGranted(  SELF::ACCESS_ROLE_FOR_USERFIELD )) {
			
			$formMapper->tab('Brand')->with('Ownership')
				->add('owner', 'sonata_type_model', array(
					'required' => false,
					'expanded' => false,
					'btn_add' =>false,
					'multiple' => false
				))
				->end()->end();
			
		}
		
		$quotas_disabled = true;
		$formMapper
			->tab('Settings')
				->with('Quotas')
					->add('moderateNewProperties', null,
						[
							'label' => 'Moderate new properties',
							"disabled" => $quotas_disabled
						])
					->add('maxTotalProperties', 'number',
						[
							'label' => 'Maximum Properties Allowed in Brand',
							"disabled" => $quotas_disabled
						])
					->add('MaxTotalPropertiesPerMember', 'number',
						[
							'label' => 'Maximum Properties Allowed in Brand',
							"disabled" => $quotas_disabled
						])
				->end()
			->end();
		
	
		// Do they have providers?
		if(!$this->hasNoProviders) {
			//  Great. They have providers, let's show the chanels...
			$formMapper
				->tab('Channel')
					->with('Connected Channels',['description' => "You can push all properties within a brand to any number of connected channels. You will see all channels that you have connected to within the channel dropdown row."])
						->add('channels', 'sonata_type_collection',
							[
								'compound'     => true,
								'by_reference' => false,
								'btn_add'      => 'Create a new channel connection',
								'type_options' => [
									// Prevents the "Delete" option from being displayed
									'delete' => false
								]
							], ['edit'   => 'inline', 'inline' => 'table']	)
					->end()
				->end();
		} else {
			
			$formMapper
				->tab('Channel')
					->with('Connected Channels',[
							'box_class' => 'box box-danger',
							'description' => "You have not created any channel connections yet."  .
								"<br><br><b>How to fix this?</b><br />" .
								"Simply add some credentials for a channel."])
						->add('channels', 'sonata_type_collection',	[
								'compound'     => true,
								'by_reference' => false,
								'btn_add'      => false,
								'type_options' => [
									// Prevents the "Delete" option from being displayed
									'delete' => false
								]],	[ 'edit'   => 'inline',	'inline' => 'table' ] )
						->end()
				->end();
		}
		
	}
	
	public function getFormTheme()
	{
		
		return array_merge(
			parent::getFormTheme(),
			array('AppBundle:Admin/BrandAdmin:admin.theme.html.twig')
		);
	}
	
	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper
			->add('descriptiveName')
			->add('brandName');
	}
	
	
	
	protected function configureListFields(ListMapper $listMapper)
	{
		
		
		$listMapper
			->addIdentifier('brandName', null, array(
				'route' => array(
					'name' => 'edit'
				)
			))
			->add('descriptiveName')
			->add('owner')
			->add('members')
			->add('propertiesCount')
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
		
	
		return $query;
	}
	
	public function prePersist($entity)
	{
		
		if($entity->getOwner() === null ){
			$owner = $this->getConfigurationPool()->getContainer()->get('security.context')->getToken()->getUser();
			$entity->setOwner( $owner );
		}
		// If we are editing the brand, we ALWAYS ALWYS want to make sure that new brands are for the current brand active...
		foreach($entity->getMembers() as $member){
			$member->setBrand( $entity );
		}
		
		
	}
	
	public function postPersist($entity)
	{
		parent::postPersist($entity); // TODO: Change the autogenerated stub
		foreach($entity->getMembers() as $member){
			$member->setBrand( $entity );
			$this->createObjectSecurity($member);
		}
	}
	
	public function preUpdate($brand)
	{
		if (false === $this->isGranted('VIEW', $brand)) {
			throw new AccessDeniedException();
		}
		
		// If we are editing the brand, we ALWAYS ALWYS want to make sure that new brands are for the current brand active...
		foreach($brand->getMembers() as $member){
			$member->setBrand( $brand );
		}
		
	}
	
	
	
	
	
	
}