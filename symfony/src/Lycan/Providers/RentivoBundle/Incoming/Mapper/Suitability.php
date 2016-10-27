<?php
namespace Lycan\Providers\RentivoBundle\Incoming\Mapper;

use Pristine\Enums\Suitability as Enum;

class Suitability {
	
	private $map
		= [
			"suitableChildren_0"   => Enum::SUITABILITY_CHILDREN_NOT_ALLOWED,
			"suitableChildren_1"   => Enum::SUITABILITY_CHILDREN_WELCOME,
			"suitableElderly_0"    => Enum::SUITABILITY_ACCESSIBILITY_ELDERLY_NOT_RECOMMENDED,
			"suitableElderly_1"    => Enum::SUITABILITY_ACCESSIBILITY_ELDERLY_GREAT,
			"suitablePets_0"       => Enum::SUITABILITY_PETS_NOT_ALLOWED,
			"suitablePets_1"       => Enum::SUITABILITY_PETS_ALLOWED,
			"suitableSmoking_0"    => Enum::SUITABILITY_SMOKING_NOT_ALLOWED,
			"suitableSmoking_1"    => Enum::SUITABILITY_SMOKING_ALLOWED,
			"suitableWheelchair_0" => Enum::SUITABILITY_ACCESSIBILITY_WHEELCHAIR_NOT_ACCESSIBLE,
			"suitableWheelchair_1" => Enum::SUITABILITY_ACCESSIBILITY_WHEELCHAIR_GREAT
		];
	
	/**
	 * @return array
	 */
	public function getMap()
	{
		return $this->map;
	}
}