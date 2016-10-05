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
class ChannelBrandAdmin extends BaseAdmin
{
	
	
	
	protected function configureFormFields(FormMapper $formMapper)
	{
		
		$owner = $this->getConfigurationPool()->getContainer()->get('security.context')->getToken()->getUser();
		$em = $this->modelManager->getEntityManager('AppBundle:Brand');
		
		
		// define group zoning
		$formMapper
			->tab('Your Channel Bridge')
				->with('Brand', array('class' => 'col-md-6'))->end()
				->with('Channel Provider', array('class' => 'col-md-6'))->end()
			->end()
			;
		
		
		// We only want to be able to create IF you own the brand....
		$query = $query = $em->createQueryBuilder("b")
			->select("b")
			->from("AppBundle\Entity\Brand", "b")
			->where("b.owner = :owner")
			->setParameter("owner", $owner);
		
		// define group zoning
		$formMapper
			->tab('Your Channel Bridge')
				->with('Brand')
					->add('brand', 'sonata_type_model',
						[
							'btn_add' => false,
							'required' => true,
							'query' => $query
						]
					)->end()
			->end();

		
		// define group zoning
		$formMapper
			->tab('Your Channel Bridge')
				->with('Channel Provider')
					->add('provider', 'sonata_type_model',
					[
						'btn_add' => false,
						'required' => true,
						
						// 'class' => 'Lycan\Providers\RentivoBundle\Entity\ProviderRentivoAuth',

					]
				);
		
		
		
		
		
	}
	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		
	}
	
	
	
	protected function configureListFields(ListMapper $listMapper)
	{
		
		$listMapper->addIdentifier('id')
			->add('brand')
			->add('provider')->add('_action', 'actions', array(
				'actions' => array(
					'edit' => array(),
					'delete' => array(),
				)
			));
		
		
	}
	
	
	
	
}