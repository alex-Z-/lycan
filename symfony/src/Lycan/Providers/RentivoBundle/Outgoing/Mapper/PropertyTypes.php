<?php
namespace Lycan\Providers\RentivoBundle\Outgoing\Mapper;

use Pristine\Enums\ListingTypes as Enum;
use Lycan\Providers\RentivoBundle\Incoming\Mapper\PropertyTypes as RentivoTypes;

class PropertyTypes extends RentivoTypes {
	
	public function getMap()
	{
		$map = parent::getMap();
		return array_flip($map);
	}
}