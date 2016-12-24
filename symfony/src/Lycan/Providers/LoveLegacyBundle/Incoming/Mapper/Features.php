<?php
namespace Lycan\Providers\LoveLegacyBundle\Incoming\Mapper;
use Pristine\Enums\Features as Enum;
use Pristine\Mapper\Features as BaseFeatures;

class Features extends  BaseFeatures {
	private $map
		= [
	
			];
	
	public function getMap()
	{
		return array_merge( parent::getMap(), $this->map );
	}
}