<?php
namespace Lycan\Providers\RentivoBundle\Incoming\Hydrator;
use Incoming;
use AppBundle\Schema\Container as SchemaContainer;
class SchemaHydrator implements Incoming\Hydrator\HydratorInterface
{
	public function hydrate( $input, $model)
	{
		dump($input);
		$model->set('name', $input->get('name'));
		
				
		return $model;
	}
}