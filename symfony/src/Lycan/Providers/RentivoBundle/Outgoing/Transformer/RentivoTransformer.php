<?php
namespace Lycan\Providers\RentivoBundle\Outgoing\Transformer;
use Incoming;
use Pristine\Schema\Container as SchemaContainer;
class RentivoTransformer implements Incoming\Transformer\TransformerInterface
{
	public function transform($json)
	{
		 $container = new SchemaContainer(  );
		 $input = $container->fromArray( json_decode($json, true) );
	 	 return $input;
	}
}