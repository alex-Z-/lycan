<?php
namespace Lycan\Providers\TabsBundle\Incoming\Transformer;
use Incoming;
use Pristine\Schema\Container as SchemaContainer;
class TabsTransformer implements Incoming\Transformer\TransformerInterface
{
	public function transform($input)
	{
		 $container = new SchemaContainer(  );
		 $input = $container->fromArray($input);
	 	 return $input;
	}
}