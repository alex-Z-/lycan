<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
class BaseAdmin extends AbstractAdmin
{
	protected $container;
	public function setContainer($container){
		$this->container = $container;
	}
	
	public function addBundledSubClasses($configuration = null){
		
		
		$providers = $this->container->getParameter("lycan.core.providers");
		$classes = [];
		foreach($providers as $p){
			$classes[$p['name']] = $p['adminClass'];
		}
		
		$this->setSubClasses($classes);

	}
	
	public function configure(){
		
		$em =  $this->getConfigurationPool()->getContainer()->get('doctrine')->getEntityManager();
		$em->getFilters()->enable('softdeleteable');
		$this->setTemplate('button_create', 'AppBundle:Admin/Button:create_button.html.twig');
	}
	
	public function getButtonLabel($type = "create"){
		
		switch($type){
			case "create":
				return "Create New " . $this->getClassnameLabel();
			default:
				return null;
		}
		
	}
	
}