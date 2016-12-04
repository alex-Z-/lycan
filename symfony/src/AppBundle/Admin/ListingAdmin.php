<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\CoreBundle\Validator\ErrorElement as ErrorElement;
use ListingSchema\Load;
use JsonSchema\Constraints\Factory;
use JsonSchema\Constraints\Constraint;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;


class ListingAdmin extends BaseAdmin
{
	protected $datagridValues = array(
		'_page' => 1,
		'_sort_order' => 'DESC',
		'_sort_by' => 'createdAt'
	);
	const  ACCESS_ROLE_FOR_USERFIELD ="MASTER";

	protected function configureFormFields(FormMapper $formMapper)
	{
		// This is the current logged in.. but I think we actually want the
		$user = $this->getConfigurationPool()->getContainer()->get('security.context')->getToken()->getUser();
		$accessToUserFields = ( $this->isGranted(  SELF::ACCESS_ROLE_FOR_USERFIELD ) && $this->getSubject()->getId() === null);
		$em = $this->modelManager->getEntityManager('AppBundle:Brand');
		
		if($this->getSubject()->getId() && $this->getSubject()->getOwner() ){
			$user =   $this->getSubject()->getOwner();
		}
		
		// WE ABSOLUTELY WANT THE EXISTING BRAND TO ALSO SHOW...
		$brandsQuery =  $query = $em->createQueryBuilder("b")
			->select("b")
			->from("AppBundle:Brand", "b")
			->leftjoin("b.members", "m")
			->where("b.owner = :owner or m.member = :owner")
			->setParameter("owner", $user->getId() );
		
		if( $this->getSubject()->getBrands() && $brand = $this->getSubject()->getBrands()->current() ){
			$brandsQuery->orWhere("b.id = :currentBrand")
				->setParameter("currentBrand", $brand->getId() );
		}
		
		
		// define group zoning
		$formMapper
			->tab('Listing')
				->with('Property Name', array('class' => 'col-md-7'))->end()
			->end()
			->tab('Schema')
				->with('Property Schema', array('class' => 'col-md-12'))->end()
			->end();
		
		$formMapper
			->tab('Listing')
				->with('Property Name',	array(
						'box_class' => 'box box-warning',
						'description' => "Create your property and give it a descriptive name. <br />This name is just a useful internal nickname, and does not need to relate to the marketing name for your rental. "	))
					->add('descriptiveName', 'text')
					->add('provider', null , ['disabled' => true])
				->end()
			->end();
		
		// We don't want to let properties be transfered until we understand more of the implications.
		
		
		// TODO. The problem Is that ID is being updated. Need to find a new way to create the link in the
		// <a href="{{ path('admin_app_listing_edit', {'id' :  nested_field.vars.data })}}">Edit</a>
		$formMapper->tab('Schema')
				->with('Property Schema')
					->add('schemaObject', 'textarea', array(
						'attr' => array( 'rows' => '10'),
					))
				->end()
			->end();
		
		
	}
	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper->add('descriptiveName')
			;
	}
	
	
	// add this method
	public function validate(ErrorElement $errorElement, $object)
	{
		
		
		$schemaDefinition = json_decode( Load::getInstance()->load() );
		
		// Provide $schemaStorage to the Validator so that references can be resolved during validation
		$schemaStorage = new SchemaStorage();
		$schemaStorage->addSchema('file://list-schema', $schemaDefinition);
		$validator = new Validator(new Factory(
			$schemaStorage,
			null,
			Constraint::CHECK_MODE_TYPE_CAST | Constraint::CHECK_MODE_COERCE
		));
		$validator->check(   json_decode( $object->getSchemaObject() ) ,  $schemaDefinition );
		
		if(!$validator->isValid()){
			$errorElement
				->with('schemaObject')
				->addViolation('Does not conform to valid schema format.')
				->end();
			
			if($validator->getErrors()){
				foreach($validator->getErrors() as $error){
					
					$errorElement
						->with('schemaObject')
						->addViolation($error['message'] . " - " .  $error['property'] . " points to: " . $error['pointer'])
						->end();
				}
			}
			
		}
		
		
		
		
		
	}
	
	public function preRemove($property)
	{
		dump($property);die();
		
	}
	
	
	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper->addIdentifier('descriptiveName', null, array(
				'route' => array(
					'name' => 'edit'
				)
			));
			
		
		$listMapper->add('master');
		//
		// , 'stemplate' => 'AppBundle:Admin/PropertyAdmin:list_provider.html.twig'
		$listMapper->add('provider', null, [ 'associated_property' => 'typeAndName' ]);
		
		$listMapper->add('providerListingId', 'string');
		
		
		$listMapper->add('isSchemaValid');
		$listMapper->add('updatedAt');
		$listMapper->add('_action', 'actions', array(
		'actions' => array(
			'edit' => array(),
			'delete' => array(),
		)));
		
	}
	
	
	protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
	{
		
		if (!$childAdmin && !in_array($action, array('edit'))) {
			return;
		}
		// NOT SURE>..
		if($childAdmin) {
			return $childAdmin->configureSideMenu($menu, $action);
		}
		
		$admin = $this->isChild() ? $this->getParent() : $this;
		
		// $id = $admin->getRequest()->get('id');
		$router = $this->getConfigurationPool()->getContainer()->get('router');
		
		if($admin->getSubject()->getMaster()) {
			
			$menu->setExtra( 'safe_label' , true );
		
			
			
			$menu->addChild(
				"Master Property",
				array(
					'uri' => $router->generate('admin_app_property_edit', array('id' => $admin->getSubject()->getMaster()->getId() )),
					'label' => "Go up to Master Property"
				
				)
			)->setExtra( 'safe_label' , true );
		}
		
		if ( $this->isGranted("ROLE_SUPERADMIN")  ){
			
			$menu->addChild(
				$this->trans('Show Event Log', array(), 'SonataUserBundle'),
				array('uri' => $router->generate('admin_app_property_event_list', array('id' => $admin->getSubject()->getId() )))
			);
			
			
		}
		
		
	}
	
	
	
}