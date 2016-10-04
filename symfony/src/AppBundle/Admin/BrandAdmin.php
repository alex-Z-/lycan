<?php

namespace AppBundle\Admin;

use AppBundle\AppBundle;
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
			->end()
			->tab('Members')
			->with('Members', array('class' => 'col-md-5'))->end()
			->end();
		
		
		$em = $this->modelManager->getEntityManager('AppBundle:Brand');
		$owner = $this->getConfigurationPool()->getContainer()->get('security.context')->getToken()->getUser();
	
		$query =  $query = $em->createQueryBuilder("b")
			->select("u")
			->from("Application\Sonata\UserBundle\Entity\User", "u")
			->where("u.id <> :owner and u.username <> 'admin'")
			->setParameter("owner", $owner->getId() );
		
		
		$formMapper
			->tab('Brand')
			->with("Details")
				->add('descriptiveName', 'text')
				->add('brandName', 'text')->end()
			->end()
			->tab('Members')
			->with("Members", [
						'box_class' => 'box box-warning',
						'description' => "The members field allows you to add other users to add their rentals to your brand. For example, if you allow other users to push properties to your brand you can add them here."
					]);
		
		
		$formMapper
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
							'inline' => 'table',
							'allow_delete' => false,
							"foo" => "bar"
						)
				)
			->end()->end();
		
		// We don't want to let properties be transfered until we understand more of the implications.
		if ($this->isGranted(  SELF::ACCESS_ROLE_FOR_USERFIELD )) {
			
			$formMapper->tab('Brand')->with('Ownership')
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
			->add('members')
			
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
	
	public function preUpdate($brand)
	{
		$securityContext = $this->getConfigurationPool()->getContainer()->get('security.context');
		$manager = $this->getConfigurationPool()->getContainer()->get('oneup_acl.manager');
		if (false === $this->isGranted('VIEW', $brand)) {
			throw new AccessDeniedException();
		}
		
		// If we are editing the brand, we ALWAYS ALWYS want to make sure that new brands are for the current brand active...
		foreach($brand->getMembers() as $member){
			$member->setBrand( $brand );
		}
				
		
		// $manager->revokeAllObjectPermissions($brand);
		// die("pre update");
		
	}
	
	
	
	protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
	{
		
	
		
		
		
	}
	
	
}