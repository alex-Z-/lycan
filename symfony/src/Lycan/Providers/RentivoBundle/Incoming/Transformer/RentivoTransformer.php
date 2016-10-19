<?php
namespace Lycan\Providers\RentivoBundle\Incoming\Transformer;
use Incoming;
use Pristine\Schema\Container as SchemaContainer;
class RentivoTransformer implements Incoming\Transformer\TransformerInterface
{
	public function transform($input)
	{
		 $container = new SchemaContainer(  );
		 $input = $container->fromArray($input);
	 	 return $input;
	}
}