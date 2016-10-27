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


class PropertyAdmin extends BaseAdmin
{
	protected $datagridValues = array(
		'_page' => 1,
		'_sort_order' => 'DESC',
		'_sort_by' => 'createdAt'
	);
	const  ACCESS_ROLE_FOR_USERFIELD ="MASTER";
	protected $parentAssociationMapping = 'brands';
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
			->tab('Property')
				->with('Property Name', array('class' => 'col-md-7'))->end()
				->with('Ownership', array('class' => 'col-md-5'))->end()
				->with('Brand', array('class' => 'col-md-5'))->end()
			->end()
			->tab('Schema')
				->with('Property Schema', array('class' => 'col-md-12'))->end()
			->end();
		
		$formMapper
			->tab('Property')
				->with('Property Name',	array(
						'box_class' => 'box box-warning',
						'description' => "Create your property and give it a descriptive name. <br />This name is just a useful internal nickname, and does not need to relate to the marketing name for your rental. "	))
					->add('descriptiveName', 'text')
				->end()
				->with("Brand" , [
						'box_class' => 'box box-primary',
						'description' => "Each property on your account can be assigned to a brand. You will most likely only have a single brand, but maintaining brands is an easy way to classify and group similar rentals in a manageable process." .
										"<br /><p><b>Working with other managers?</b><br />Brands which have been shared with you will also show up here.</p>"])
					->add('brands', 'sonata_type_model',
						array(
							'btn_add' => false,
							'by_reference' => false,
							'expanded' => false,
							'multiple' => true,
							'label' => 'Brands',
							'query' => $brandsQuery
						)
					)->end();
		
		// We don't want to let properties be transfered until we understand more of the implications.
		
		$formMapper
				->with('Ownership')
				->add('owner', 'sonata_type_model', array(
					'required' => false,
					'btn_add' => ($accessToUserFields) ? "Create a new user" : false,
					"disabled" => !$this->isGranted(  SELF::ACCESS_ROLE_FOR_USERFIELD ),
				))
				->end()
			->end();
		
		$formMapper->tab('Schema')
				->with('Property Schema')
					->add('schemaObject', 'textarea', array(
						'attr' => array( 'rows' => '10'),
					))
				->end()
			->end();;
		
	}
	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper->add('descriptiveName')
			->add('owner')
			->add('brands');
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
	
	
	
	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper->addIdentifier('id', null, array(
				'route' => array(
					'name' => 'edit'
				)
			))
			->add('descriptiveName', null, array( 'template'=>'AppBundle') );
		
		$listMapper->add('brands');
		
		if ($this->isGranted( SELF::ACCESS_ROLE_FOR_USERFIELD ) ) {
			$listMapper->add('owner');
		}
		
		$listMapper->add('isSchemaValid');
		
		$listMapper->add('_action', 'actions', array(
		'actions' => array(
			'edit' => array(),
			'delete' => array(),
		)));
		
	}
	
	public function createQuery($context = 'list')
	{
		$query = parent::createQuery($context);
		
		if ( !$this->isGranted("ROLE_SUPERADMIN") ||  !$this->isGranted('MASTER') ){
			$owner = $this->getConfigurationPool()->getContainer()->get('security.context')->getToken()->getUser();
			// Is this correct? What about OR property?
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
	
	
}