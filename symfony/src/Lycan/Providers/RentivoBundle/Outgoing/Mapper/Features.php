<?php
namespace Lycan\Providers\RentivoBundle\Outgoing\Mapper;
use Pristine\Enums\Features as Enum;
use Lycan\Providers\RentivoBundle\Incoming\Mapper\Features as RentivoFeatures;

class Features extends  RentivoFeatures {
	
	public function getMap()
	{
		@ $map = parent::getMap();
		// Suppress warning when array can't map to a flipped array.
		return @array_flip($map);
	}
}