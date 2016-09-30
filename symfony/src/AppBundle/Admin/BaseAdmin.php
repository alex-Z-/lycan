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
	
	public function configure(){
		
		
		
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