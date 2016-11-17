<?php
namespace Lycan\Providers\TabsBundle\Incoming\Hydrator;
use Incoming;
use Pristine\Schema\Container as SchemaContainer;
use Pristine\Enums;
use Lycan\Providers\TabsBundle\Incoming\Mapper;
use Pristine\Mapper\Mapping;
use Pristine\Utils\LocaleUtils;

class SchemaHydrator implements Incoming\Hydrator\HydratorInterface
{
	public function hydrate( $input, $model)
	{
	
		dump($input);die();
		return $model;
	}
	
	
}