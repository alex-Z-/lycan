<?php
namespace Lycan\Providers\SupercontrolBundle\Incoming\Mapper;
use Pristine\Enums\Features as Enum;
use Pristine\Mapper\Features as BaseFeatures;

class Features extends  BaseFeatures {
	private $map
		= [
			"Hot Tub" => Enum::SPA_POOL_JACUZZI_HOT_TUB,
			"Snooker / Pool Table" => Enum::ENTERTAINMENT_SNOOKER_TABLE,
			"Wifi" => Enum::COMMUNICATION_INTERNET_WIFI,
			"Car Parking"	=> Enum::GENERAL_PARKING_SPACE,
			"Outdoor Pizza Oven" => Enum::OUTDOOR_OUTSIDE_STONE_PIZZA_OVEN,
			"Dogs"	=> Enum::GENERAL_PET_FRIENDLY
		];
	
	public function getMap()
	{
		return array_merge( parent::getMap(), $this->map );
	}
}