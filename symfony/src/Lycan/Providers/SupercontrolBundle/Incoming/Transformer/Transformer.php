<?php
namespace Lycan\Providers\SupercontrolBundle\Incoming\Transformer;
use Incoming;
use Pristine\Schema\Container as SchemaContainer;
class Transformer implements Incoming\Transformer\TransformerInterface
{
	public function transform($input)
	{
		
		 $input = $this->arrayRemoveEmpty($input);
		 $container = new SchemaContainer(  );
		 $input = $container->fromArray($input);
	 	 return $input;
	}
	
	function arrayRemoveEmpty($haystack)
	{
		foreach ($haystack as $key => $value) {
			if (is_array($value)) {
				$haystack[$key] = $this->arrayRemoveEmpty($haystack[$key]);
			}
			
			if (empty($haystack[$key])) {
				unset($haystack[$key]);
			}
		}
		
		return $haystack;
	}
}