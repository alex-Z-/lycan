<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
class PropertyAdmin extends BaseAdmin
{
	
	const  ACCESS_ROLE_FOR_USERFIELD ="MASTER";
	
	

	
	protected function configureFormFields(FormMapper $formMapper)
	{
		
		// define group zoning
		$formMapper
			->tab('Property')
				->with('Property Name', array('class' => 'col-md-7'))->end()
				->with('Ownership', array('class' => 'col-md-5'))->end()
				->with('Brand', array('class' => 'col-md-5'))->end()
			->end();
		
		
		$formMapper
			->tab('Property')
				->with('Property Name',
					array(
						'box_class' => 'box box-warning',
						'description' => "Create your property and give it a descriptive name. <br />This name is just a useful internal nickname, and does not need to relate to the marketing name for your rental. "
					))
					->add('descriptiveName', 'text')->end()
				->with("Brand" ,[
						'box_class' => 'box box-primary',
						'description' => "Each property on your account can be assigned to a brand. You will most likely only have a single brand, but maintaining brands is an easy way to classify and group similar rentals in a manageable process." .
										"<br /><p><b>Working with other managers?</b><br />Brands which have been shared with you will also show up here.</p>"
					])
					->add('brands', 'sonata_type_model',
						array(
							'btn_add' => false, 'by_reference' => false, 'expanded' => false, 'multiple' => true, 'label' => 'Brands'
						)
					)->end();
		
	
		// We don't want to let properties be transfered until we understand more of the implications.
		if ($this->isGranted(  SELF::ACCESS_ROLE_FOR_USERFIELD ) && $this->getSubject()->getId() === null) {
			
			$formMapper->with('Ownership')
				->add('owner', 'sonata_type_model', array(
					'required' => false,
					'expanded' => false,
					'btn_add' => "Create a new user",
					'multiple' => false
				))
				->end();
		
		} else if( $this->isGranted(  SELF::ACCESS_ROLE_FOR_USERFIELD ) ){
			$formMapper->with('Ownership')
				->add('owner', 'sonata_type_model', array(
					'required' => false,
					'expanded' => false,
					"disabled" => true,
					'btn_add' => false,
					'multiple' => false
				))
				->end();
		}
	
		
		
	}
	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper->add('descriptiveName')
			->add('owner')
			->add('brands');
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
		
		$listMapper->add('_action', 'actions', array(
		'actions' => array(
			'edit' => array(),
			'delete' => array(),
		)
	));
		
	}
	
	public function createQuery($context = 'list')
	{
		$query = parent::createQuery($context);
		
		if ( !$this->isGranted("ROLE_SUPERADMIN") ||  !$this->isGranted('MASTER') ){
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