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
use Sonata\CoreBundle\Validator\ErrorElement;

use JsonSchema\Constraints\Factory;
use JsonSchema\Constraints\Constraint;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;

class PolicyAdmin extends BaseAdmin
{
	
	
	const  ACCESS_ROLE_FOR_USERFIELD ="MASTER";
	
	
	protected $container;
	public function setContainer($container){
		$this->container = $container;
	}
	
	
	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper->addIdentifier('descriptiveName', null, array(
				'route' => array(
					'name' => 'edit'
				)
			))
			->add('createdAt');
		
		$listMapper->add('_action', 'actions', array(
			'actions' => array(
				'edit' => array(),
				'delete' => array(),
			)));
		
		
		
	}
	
	protected function configureFormFields(FormMapper $formMapper)
	{
		
		// define group zoning
		$formMapper
			->tab('Policy')
				->with('Policy', array('class' => 'col-md-7'))->end()
			->end()		;
		
		$formMapper
			->tab('Policy')
			->with('Policy',	array(
				'box_class' => 'box box-warning',
				'description' => "The policy requirements for validating schemas."	))
			->add('descriptiveName', 'text')
			->add('policySchema', 'textarea', array(
				'attr' => array( 'rows' => '10'),
			))
			->end()
			->end();
		
		
	}
	
	
	// add this method
	public function validate( ErrorElement $errorElement, $object)
	{
		
		
		$schemaDefinition = json_decode( file_get_contents(__DIR__ . '/../Resources/schema/JsonSchema.json') );
	
		// Provide $schemaStorage to the Validator so that references can be resolved during validation
		$schemaStorage = new SchemaStorage();
		$schemaStorage->addSchema('file://list-schema', $schemaDefinition);
		$validator = new Validator(new Factory(
			$schemaStorage,
			null,
			Constraint::CHECK_MODE_TYPE_CAST | Constraint::CHECK_MODE_COERCE
		));
		$validator->check(   json_decode( $object->getPolicySchema() ) ,  $schemaDefinition );
	
		if(!$validator->isValid()){
			$errorElement
				->with('Policy')
				->addViolation('Does not conform to valid schema format.')
				->end();
			
			if($validator->getErrors()){
				foreach($validator->getErrors() as $error){
					
					$errorElement
						->with('policySchema')
						->addViolation($error['message'] . " - " .  $error['property'] . " points to: " . $error['pointer'])
						->end();
				}
			}
			
		}
		
		
		
		
		
	}
	
	
	
}