<?php
/**
 * Created by IntelliJ IDEA.
 * User: layke
 * Date: 27/10/16
 * Time: 13:26
 */

namespace AppBundle\Importer;

use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use ListingSchema\Load;
use JsonSchema\Constraints\Factory;
use JsonSchema\Constraints\Constraint;
use Pristine\Schema\Container;
use Application\Sonata\UserBundle\Entity\User;
use AppBundle\Entity\Property;
class Importer {
	
	private $logger; // Monolog-logger.
	private $container;
	private $em;
	
	public function __construct( $logger, $em )
	{
		$this->logger = $logger->logger;
		$this->em = $em;
		
	}
	
	public function setContainer($container){
		$this->container = $container;
	}
	
	
	
	public function import(Container $schema, $provider){
		
		//$schemaDefinition = json_decode(file_get_contents(__DIR__.'/test.json'));
		$schemaDefinition = json_decode( Load::getInstance()->load() );
		
		$batchLogger = $this->container->get('app.logger.jobs');
	
		
		// Provide $schemaStorage to the Validator so that references can be resolved during validation
		$schemaStorage = new SchemaStorage();
		$schemaStorage->addSchema('file://list-schema', $schemaDefinition);
		$validator = new Validator(new Factory(
			$schemaStorage,
			null,
			Constraint::CHECK_MODE_TYPE_CAST | Constraint::CHECK_MODE_COERCE
		));
		
		// This does two things:
		// 1) Mutates $jsonSchemaObject to normalize the references (to file://mySchema#/definitions/integerData, etc)
		// 2) Tells $schemaStorage that references to file://mySchema... should be resolved by looking in $jsonSchemaObject
		
		
		$validator->check(  json_decode(json_encode( $schema->toArray() ) ),  $schemaDefinition );
		if($validator->isValid()){
			
			$batchLogger->info("Importing Schema - Valid", [ "input" => $schema->toArray() ] );
			
			
			$this->logger->debug("Schema was valid." );
			$this->logger->info( "Schema will be imported or upserted to user account", $provider->getOwner()->getLogValues() );
			$property = $this->_upsert($schema, $provider);
			$property->setIsSchemaValid(true);
			$this->em->persist($property);
			$this->em->flush();
		} else {
			$this->logger->warning("Schema was not found to be valid.", $validator->getErrors() );
			$batchLogger->warning("Importing Schema - Schema was found to be invalid", [ "input" => $schema->toArray(), "output" => $validator->getErrors() ] );
			$property = $this->_upsert($schema, $provider);
			$property->setIsSchemaValid(false);
			$property->setSchemaErrors($validator->getErrors());
			$this->em->persist($property);
			$this->em->flush();
			
			
		}
		
		return $property;
		
	}
	
	private function _upsert(Container $schema, $provider){
		// Need to find out if there is a schema that exists already for the particular rental.
		// Composite Key Query :::->
		// $id from the schema.
		// Reference to the User
		$property = new Property();
		
		$property->setDescriptiveName( $schema->get("name") );
		$property->setOwner( $provider->getOwner() );
		$property->setSchemaObject($schema->toJson());
		return $property;
	}
}