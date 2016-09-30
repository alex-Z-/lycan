<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
class BrandAdmin extends BaseAdmin
{
	
	const  ACCESS_ROLE_FOR_USERFIELD ="ROLE_SUPERADMIN";
	
	protected function configureFormFields(FormMapper $formMapper)
	{
		// define group zoning
		$formMapper
			->tab('Brand')
			->with('Details', array('class' => 'col-md-7'))->end()
			->with('Ownership', array('class' => 'col-md-5'))->end()
			->with('Members', array('class' => 'col-md-5'))->end()
			->end();
		
		
		
		
		$formMapper
			->tab('Brand')
			->with("Details")
				->add('descriptiveName', 'text')
				->add('brandName', 'text')->end()
			->with("Members", [
						'box_class' => 'box box-warning',
						'description' => "The members field is how you add access to your brand to other users. For example, if you allow other users to push properties to your brand you can add them here."
					])
				->add('users', 'sonata_type_model',
						array(
							'help' => 'Set the title of a web page',
							'sonata_help' => "Add Users to this brand",
							'sonata_field_description' => "Add Users to this brand",
							'btn_add' => false, 'by_reference' => false, 'expanded' => false, 'multiple' => true, 'label' => 'Users'
						)
				)
			->end();
		
		// We don't want to let properties be transfered until we understand more of the implications.
		if ($this->isGranted(  SELF::ACCESS_ROLE_FOR_USERFIELD )) {
			
			$formMapper->with('Ownership')
				->add('owner', 'sonata_type_model', array(
					'required' => false,
					'expanded' => false,
					'btn_add' =>false,
					'multiple' => false
				))
				->end();
			
		}
		
		
		
	}
	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper->add('descriptiveName')
			->add('brandName');
	}
	
	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper->addIdentifier('brandName', null, array(
				'route' => array(
					'name' => 'edit'
				)
			))
			->add('descriptiveName')
			->add('owner')
			->add('users')
			
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
	
	public function prePersist($brand)
	{
		
		
	}
	
	
	protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
	{
		
	
		
		
		
	}
	
	
}