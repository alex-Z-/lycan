<?php
namespace Lycan\Providers\RentivoBundle\Incoming\Hydrator;
use Incoming;
use Pristine\Schema\Container as SchemaContainer;
use Pristine\Enums;
use Lycan\Providers\RentivoBundle\Incoming\Mapper;
use Pristine\Mapper\Mapping;
class SchemaHydrator implements Incoming\Hydrator\HydratorInterface
{
	public function hydrate( $input, $model)
	{
		
		
		// With mapping, you pass a "ENUM" file, the "Mapping" definitions.
		$mapper = new Mapping( 'Pristine\Enums\ListingTypes', new Mapper\PropertyTypes() );
		// dump($mapper->map( $input->get("attributes.propertyType") ));die();
		$model->set('name', $input->get('name'));
		$model->set('listing.type', $mapper->map( $input->get("attributes.propertyType") ) );
		
		dump($input, $model);
		die();
				
		return $model;
	}
}