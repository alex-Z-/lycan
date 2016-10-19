<?php
namespace Lycan\Providers\RentivoBundle\Incoming\Mapper;
use Pristine\Enums\ListingTypes as Enum;

class PropertyTypes {
	
	private $map = [
		"Apartment" => Enum::LISTING_TYPE_APARTMENT,
		"House" => Enum::LISTING_TYPE_HOUSE,
		"Villa" => Enum::LISTING_TYPE_VILLA,
		"Townhouse" => Enum::LISTING_TYPE_TOWNHOUSE,
		"Chalet" => Enum::LISTING_TYPE_CHALET,
		"Farmhouse/Barn" => Enum::LISTING_TYPE_FARMHOUSE,
		"Castle" => Enum::LISTING_TYPE_CASTLE,
		"Cottage" => Enum::LISTING_TYPE_COTTAGE,
		"Resort" => Enum::LISTING_TYPE_RESORT,
		"B&B" => Enum::LISTING_TYPE_BNB,
		"Hotel" => Enum::LISTING_TYPE_HOTEL,
		"Yacht/Boat" => Enum::LISTING_TYPE_BOAT,
		"Condo" => Enum::LISTING_TYPE_CONDO,
		"Houseboat" => Enum::LISTING_TYPE_HOUSEBOAT,
		"Cabin" => Enum::LISTING_TYPE_CABIN,
		"Hostel" => Enum::LISTING_TYPE_HOSTEL,
		"Beach Hut" => Enum::LISTING_TYPE_BEACHHUT,
		"Log Cabin" => Enum::LISTING_TYPE_CABIN,
		"Gite" => Enum::LISTING_TYPE_GITE,
		"Studio" => Enum::LISTING_TYPE_STUDIO,
		"Bungalow" => Enum::LISTING_TYPE_BUNGALOW,
		"Chateau" => Enum::LISTING_TYPE_CHATEAU,
		"Static Caravan" => Enum::LISTING_TYPE_CARAVAN,
		"Penthouse" => Enum::LISTING_TYPE_PENTHOUSE,
		"Campervan" => Enum::LISTING_TYPE_CAMPERVAN,
		"Suite" => Enum::LISTING_TYPE_SUITE,
	];
	
	/**
	 * @return array
	 */
	public function getMap()
	{
		return $this->map;
	}
}