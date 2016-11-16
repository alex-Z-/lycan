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

class BatchExecutionsAdmin extends BaseAdmin
{
	
	protected $parentAssociationMapping = 'provider';
	protected $baseRoutePattern = 'jobs';
	
	
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
		
		$collection->add('executions', $this->getRouterIdParameter() . '/executions');
    }
	
	protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
	{
		
		if (!$childAdmin && !in_array($action, array('edit', 'show'))) {
			return;
		}
	
		$admin = $this->isChild() ? $this->getParent() : $this;
		$router = $this->getConfigurationPool()->getContainer()->get('router');
		
	
		if( $childAdmin === null && $admin->getSubject()->getId()) {
			
			$menu->addChild(
				$this->trans('View Events in Job', array(), 'SonataUserBundle'),
				array('uri' => $router->generate('admin_providers_core_batchexecutions_event_list',
					array(
						'id' => (string) $admin->getSubject()->getId(),
						// 'filter[brands][value]' => (string)  $admin->getSubject()->getId()
					)
				))
			);
			
		}
		
		
		if($childAdmin && $childAdmin->getCode() === "admin.lycan.events"){
			
			$menu->setExtra( 'safe_label' , true );
			$menu->addChild(
				'Refresh', array(
					'uri' => $_SERVER['REQUEST_URI'],
					'label' => "Refresh &nbsp; <i class='fa fa-refresh'></i>"
				)
			)->setExtra( 'safe_label' , true );
			
			$menu->addChild(
				'Back to All Jobs',
				array('uri' => $router->generate('admin_providers_core_batchexecutions_list',
					array(
						'id' => (string) $admin->getSubject()->getId(),
						// 'filter[brands][value]' => (string)  $admin->getSubject()->getId()
					)
				))
			);
			
			
			if($action === "show") {
				$menu->addChild(
					$this->trans('View Events in Job', [], 'SonataUserBundle'),
					['uri' => $router->generate('admin_providers_core_batchexecutions_event_list',
						[
							'id' => (string)$admin->getSubject()
								->getId(),
							// 'filter[brands][value]' => (string)  $admin->getSubject()->getId()
						]
					)]
				);
			}
		}
		
	}
	
	
	public function getBatchActions()
	{
		$actions = parent::getBatchActions();
		unset($actions['delete']);
		
		return $actions;
	}
    
	protected function configureFormFields(FormMapper $formMapper)
	{
		
		// define group zoning
		$formMapper
			->tab('Executions')
			
			->end();
		
		return $formMapper;
		
	}
	
	protected function configureShowFields(ShowMapper $showMapper)
	{
		// here we set the fields of the ShowMapper variable,
		// $showMapper (but this can be called anything)
		$showMapper
			->tab('General') // the tab call is optional
			->with('Batch Execution', array(
				'class'       => 'col-md-8',
				'box_class'   => 'box box-solid',
				'description' => 'Lorem ipsum',
			))
			->add('id')
			->add('createdAt')
			->add('updatedAt')
			->add('eventsInJob')
			// ...
			->end()
			->end()
		;
		
	}
	
	
	
	protected function configureListFields(ListMapper $listMapper)
	{
		
		
		$listMapper->addIdentifier('id', null, array(
				'route' => array(
					'name' => 'show'
				)
			))
			
			->add('provider', null, [ 'label' => 'Channel', 'route' => [ 'name' => 'edit']])
			->add('createdAt')
			->add('eventsInJob', 'string', [] )
			->add('_action', 'actions', array(
				'actions' => array(
					// 'CoreBundle:CRUD:list__action_pull.html.twig'
				  'events' =>  array('template' => 'CoreBundle:Admin/BatchExecutions:Button/list__action_events.html.twig'),
				)
			));
		
		
		
		
	}
	
	
	
	
}