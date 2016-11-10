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

class EventsAdmin extends BaseAdmin
{
	protected $parentAssociationMapping = 'batch';
	protected $baseRoutePattern = 'events';
	
	
	protected $datagridValues = array(
		'_page' => 1,
		'_sort_order' => 'DESC',
		'_sort_by' => 'createdAt',
	);
	
	
	protected $container;
	public function setContainer($container){
		$this->container = $container;
	}
	
	protected function configureRoutes(RouteCollection $collection) {
		// to remove a single route
		$collection->remove('delete');
		$collection->remove('create');
	
	}
	
	public function getBatchActions()
	{
		$actions = parent::getBatchActions();
		unset($actions['delete']);
		
		return $actions;
	}
	
	protected function configureShowFields(ShowMapper $showMapper)
	{
		// here we set the fields of the ShowMapper variable,
		// $showMapper (but this can be called anything)
		$showMapper
			->tab('General') // the tab call is optional
				->with('Event Output', array(
					'class'       => 'col-md-12',
					'box_class'   => 'box box-solid',
					'description' => 'Lorem ipsum',
				))
					->add('log')
				->end()
				
			->with('Batch Execution', array(
					'class'       => 'col-md-7',
					'box_class'   => 'box box-solid',
					'description' => 'Lorem ipsum',
				))
					->add('id')
					->add('levelValue')
					->add('createdAt')
					->add('updatedAt')
				->end()
			
				->with('Server Data', array(
					'class'       => 'col-md-5',
					'box_class'   => 'box box-solid',
					'description' => 'Lorem ipsum',
				))
					->add('serverData', 'data')
					->add('context', 'data' )
			->end()
			
			
		;
		
	}

	protected function configureListFields(ListMapper $listMapper)
	{
		// app.request.get('_sonata_admin')
		$this->container->get('request')->request->set('_sonata_admin', 'admin_app_log_list');
	
		
		$listMapper
		->addIdentifier('id', null, array(
			'route' => array(
				'name' => 'show'
			)
		));
		
		$listMapper->add('level', 'html');
		$listMapper->add('batch.id');
		$listMapper->add('log');
		$listMapper->add('createdAt', 'datetime' ,  array('date_format' => 'yyyy-MM-dd HH:mm:ss') );
		$listMapper->add('_action', 'actions', array(
		'actions' => array(
			// 'CoreBundle:CRUD:list__action_pull.html.twig'
			'show' =>  array(),
		)
	));
		
		
		
	}
	
	
	
	
}