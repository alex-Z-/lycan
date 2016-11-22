<?php
namespace Lycan\Providers\TabsBundle\Incoming\Mapper;
use Pristine\Enums\Features as Enum;
use Pristine\Mapper\Features as BaseFeatures;

class Features extends  BaseFeatures {
	private $map
		= [
			"Sea View" => Enum::LOCALITY_WATER_VIEWS,
			"Water view" => Enum::LOCALITY_WATER_VIEWS,
			"Walk to Pub" => Enum::WALKABLE_PUB ,
			"Walk to Shops" => Enum::WALKABLE_SHOPPING,
			"Bicycle Storage" => Enum::GENERAL_BICYCLE_STORAGE ,
			"BBQ" => Enum::OUTDOOR_BARBECUE,
			"Garden Furniture" => Enum::OUTDOOR_GARDEN_FURNITURE,
			"Garden Enclosed" => Enum::GENERAL_GARDEN_ENCLOSED ,
			"High Chair" => Enum::GENERAL_HIGHCHAIR_AVAILABLE,
			"Cot" => Enum::GENERAL_COT_AVAILABLE ,
			"CD Player" => Enum::ENTERTAINMENT_CD_PLAYER,
			"DVD Player" => Enum::ENTERTAINMENT_DVD_PLAYER ,
			"Colour TV" => Enum::ENTERTAINMENT_STANDARD_DEFINITION_TV,
			"Mobile Reception" => Enum::GENERAL_CELLPHONE_SIGNAL,
			"Freezer" => Enum::KITCHEN_FREEZER_STANDALONE ,
			"Fridge" => Enum::KITCHEN_FRIDGE_FREEZER ,
			"Wash Machine" => Enum::GENERAL_WASHING_MACHINE,
			"Bed Linen Cost Incl" => Enum::GENERAL_BED_LINEN_INCLUDED,
			"Internet" => Enum::COMMUNICATION_INTERNET,
			"Luxury Cottage" => Enum::THEMES_LUXURY,
			];
	
	public function getMap()
	{
		return array_merge( parent::getMap(), $this->map );
	}
}