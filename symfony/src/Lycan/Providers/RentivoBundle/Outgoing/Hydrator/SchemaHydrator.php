<?php
namespace Lycan\Providers\RentivoBundle\Outgoing\Hydrator;
use Incoming;
use Pristine\ISO\CountryNames;
use Pristine\Schema\Container as SchemaContainer;
use Pristine\Enums;
use Lycan\Providers\RentivoBundle\Outgoing\Mapper;
use Pristine\Mapper\Mapping;
use Pristine\Utils\LocaleUtils;

class SchemaHydrator implements Incoming\Hydrator\HydratorInterface
{
	public function hydrate( $input, $model)
	{
	
		
		// dump($mapper->map( $input->get("attributes.propertyType") ));die();
		// TO START WITH, I don't know if we KNOW if we have already pushed the Rental to Rentivo
		// So for this reason, we won't set an ID. What eventually will need to happen, is we save the external ID of the
		// Rental as it exists in Rentivo.
		// $model->set('$id', $input->get("id"));
		$mapper = new Mapping( 'Pristine\Enums\ListingTypes', new Mapper\PropertyTypes() );
		
		$model->set('name', $input->get("name"));
		$model->set('active', true);
		$model->set('enabled', true);
		$model->set('attributes.propertyType', $mapper->map(  $input->get("listing.type") ) );
		$model->set('attributes.bedrooms', $input->get("listing.bedrooms"));
		$model->set('attributes.bathrooms', $input->get("listing.bathrooms"));
		$model->set('attributes.maxGuests', $input->get("listing.maxOccupancy"));
		$model->set('attributes.sleeps', $input->get("listing.sleeps"));
		$model->set('attributes.externalId', $input->get('$id'));
		
		
		// Location
		/*
		"location" => Container {#1073 ▼
			#elements: array:2 [▼
			"latitude" => "0"
			"longitude" => "0"
		  ]
		}
		"address" => Container {#1074 ▼
			#elements: array:6 [▼
			"addressLine1" => "Wiggalls Corner"
			"addressLine2" => "The Green"
			"city" => "Kingham"
			"stateProvince" => "Oxfordshire"
			"countryISO2" => "GB"
			"zipPostCode" => "OX7 6YD"
		  ]
		}*/
		$model->set("location.lat", $input->get("location.latitude"));
		$model->set("location.lng", $input->get("location.longitude"));
		$model->set("location.city", $input->get("address.city"));
		// $model->set("location.region", $input->get("address.city"));
		$model->set("location.stateProvince", $input->get("address.stateProvince"));
		$model->set("location.zipPostCode", $input->get("address.zipPostCode"));
		$model->set("location.country", CountryNames::getCountryName($input->get("address.countryISO2")));
		$model->set("location.iso3166", $input->get("address.countryISO2"));
		
		if($input->has("features")){
			
			$mapper = new Mapping( 'Pristine\Enums\Features', new Mapper\Features() );
			$mapper->setTolerance(0.9);
		
			foreach($input->get("features")->getIterator() as $amenity){
			
				$enum = $mapper->map( $amenity->get("type"));
				$lastMatch = $mapper->getLastMatch();
			
				if(is_string($enum)) {
					$a = [
						"namespace"    => "Amenities\\\\" . ucfirst( strtolower( current(explode("_", $amenity->get("type"))))),
						"name"        => $enum
					];
					$model->set("amenities.[]", $a);
				} else if(is_array($enum)){
				 
				} else	{
					
					$a = [
						"namespace"    => "Amenities\\\\" . ucfirst( strtolower( current(explode("_", $amenity->get("type"))))),
						"name"        =>  $amenity->get("type"),
						"comment"	=> ""
					];
					$model->set("amenities.[]", $a);
					
				}
			}
		}
		
		if($input->has("texts")){
			
			foreach($input->get("texts")->getIterator() as $text){
			
				$key = $this->getDescriptionMapping($text->get("type"));
				if($key){
					$content = $text->get("description")->first()->get("content");
					$locale = current( explode("_", $text->get("description")->first()->get("locale")));
					$model->set(sprintf($key, $locale), $content);
				}
			}
		}
		
		if($input->has("media")){
			foreach($input->get("media")->getIterator() as $media){
				if($media->get("category") === "PHOTOS" ){
					// We will be having other types eventually
					
					if($media->get("type") === "URI"){
						$image = [
							"url" => $media->get("uri"),
							"position" => $media->get("position"),
						];
						$model->set("images.[]", $image);
					}
				}
			}
		}
		
		if($input->has("pricing")){
			
			
			if($input->has("pricing.visual")){
				$model->set("pricing.summary.currency", $input->get("pricing.visual.currency"));
				if($input->has("pricing.visual.weeklyLow")){
					$model->set("pricing.summary.pricingLow", round( $input->get("pricing.visual.weeklyLow") / 7, 2) );
				}
				if($input->has("pricing.visual.weeklyHigh")) {
					$model->set("pricing.summary.pricingHigh", round($input->get("pricing.visual.weeklyHigh") / 7 , 2 ) );
				}
			}
		}
		
		if($input->has("+availability")){
			$model->set("availability.startDatum", $input->get("+availability.startDatum"));
			$model->set("availability.availability", $input->get("+availability.sequence"));
		}
				
		return $model;
	}
	
	public function getDescriptionMapping($type){
		$key = null;
		switch($type){
			case 'PRIMARY_DESCRIPTION':
				$key = "descriptions.%s.propertyDescription";
				break;
		}
		return $key;
	}
	
	public function dtf($d){
		if(is_null($d)){
			return null;
		}
		$date = new \DateTime($d);
		return $date->format("Y-m-d\TH:i:s\Z");
	}
}