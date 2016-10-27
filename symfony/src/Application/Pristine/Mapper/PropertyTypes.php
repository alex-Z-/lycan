<?php
namespace Pristine\Mapper;
use Pristine\Enums\ListingTypes as Enum;

class PropertyTypes {
	
	private $map
		= [
			"Apartment"      => Enum::LISTING_TYPE_APARTMENT,
			"B&B"            => Enum::LISTING_TYPE_BNB,
			"Beach Hut"      => Enum::LISTING_TYPE_BEACHHUT,
			"Bungalow"       => Enum::LISTING_TYPE_BUNGALOW,
			"Cabin"          => Enum::LISTING_TYPE_CABIN,
			"Campervan"      => Enum::LISTING_TYPE_CAMPERVAN,
			"Castle"         => Enum::LISTING_TYPE_CASTLE,
			"Chalet"         => Enum::LISTING_TYPE_CHALET,
			"Chateau"        => Enum::LISTING_TYPE_CHATEAU,
			"Condo"          => Enum::LISTING_TYPE_CONDO,
			"Cottage"        => Enum::LISTING_TYPE_COTTAGE,
			"Farmhouse/Barn" => Enum::LISTING_TYPE_FARMHOUSE,
			"Gite"           => Enum::LISTING_TYPE_GITE,
			"Hostel"         => Enum::LISTING_TYPE_HOSTEL,
			"Hotel"          => Enum::LISTING_TYPE_HOTEL,
			"House"          => Enum::LISTING_TYPE_HOUSE,
			"Houseboat"      => Enum::LISTING_TYPE_HOUSEBOAT,
			"Log Cabin"      => Enum::LISTING_TYPE_CABIN,
			"Penthouse"      => Enum::LISTING_TYPE_PENTHOUSE,
			"Resort"         => Enum::LISTING_TYPE_RESORT,
			"Static Caravan" => Enum::LISTING_TYPE_CARAVAN,
			"Studio"         => Enum::LISTING_TYPE_STUDIO,
			"Suite"          => Enum::LISTING_TYPE_SUITE,
			"Townhouse"      => Enum::LISTING_TYPE_TOWNHOUSE,
			"Villa"          => Enum::LISTING_TYPE_VILLA,
			"Yacht/Boat"     => Enum::LISTING_TYPE_BOAT
		];
	
	/**
	 * @return array
	 */
	public function getMap()
	{
		return $this->map;
	}
}