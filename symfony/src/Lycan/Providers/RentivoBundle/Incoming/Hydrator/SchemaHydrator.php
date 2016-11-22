<?php
namespace Lycan\Providers\RentivoBundle\Incoming\Hydrator;
use Incoming;
use Pristine\Schema\Container as SchemaContainer;
use Pristine\Enums;
use Lycan\Providers\RentivoBundle\Incoming\Mapper;
use Pristine\Mapper\Mapping;
use Pristine\Utils\LocaleUtils;

class SchemaHydrator implements Incoming\Hydrator\HydratorInterface
{
	public function hydrate( $input, $model)
	{
	
		
		// dump($mapper->map( $input->get("attributes.propertyType") ));die();
	
		$model->set('$id', $input->get("id"));
		$model->set('$schemaBuiltAt', $this->dtf($input->get("schemaBuildDate") ));
		$model->set('$createdAt', $this->dtf( $input->get("createdDate")) );
		$model->set('$lastModifiedAt', $this->dtf( $input->get("lastModified")));
		$model->set('$locale', "en_GB");
		$model->set('name', $input->get('name'));
		
		// listing.[]
		// With mapping, you pass a "ENUM" file, the "Mapping" definitions.
		$mapper = new Mapping( 'Pristine\Enums\ListingTypes', new Mapper\PropertyTypes() );
		$model->set('listing.type', $mapper->map( $input->get("attributes.propertyType") ) );
		$model->set('listing.bedrooms', $input->get("attributes.bedrooms"));
		$model->set('listing.bathrooms', $input->get("attributes.bathrooms"));
		$model->set('listing.maxOccupancy', $input->get("attributes.maxGuests")); // Additionals with extra beds
		$model->set('listing.sleeps', $input->get("attributes.maxGuests")); // Total comfortable sleeps..
		
		$model->set('location.latitude', $input->get('location.lat'));
		$model->set('location.longitude', $input->get('location.lng'));
		
		$model->set('advertisingDestination.geoplanet.woeid', $input->get('woeid.woeid'));
		
		// headlines.[]
		if($input->has("headline")) {
			foreach ($input->get("headline")
						 ->getIterator() as $locale => $headline) {
				$model->set(sprintf("headline.%s.type", $locale), "TRANSLATABLE");
				$model->set(sprintf("headline.%s.content", $locale), $headline);
				$model->set(sprintf("headline.%s.locale", $locale), $locale);
			}
		}
		
		// features.[]
		if($input->has("amenities")){
			$mapper = new Mapping( 'Pristine\Enums\Features', new Mapper\Features() );
			$mapper->setTolerance(0.9);
			foreach($input->get("amenities")->getIterator() as $amenity){
				$enum = $mapper->map( $amenity->get("name"));
				$lastMatch = $mapper->getLastMatch();
				
				if(is_string($enum)) {
					$a = [
						"category"    => "Amenities",
						"type"        => $enum,
						"+mappedFrom" => [
							"cost" => $lastMatch->get("result")->getCost(),
							"score" => $lastMatch->get("result")->getScore(),
							"name"  => $amenity->get("name")
						]
					];
					$model->set("features.[]", $a);
				} elseif(is_array($enum)){
					
					foreach($enum as $mapped){
						$a = [
							"category"    => "Amenities",
							"type"        => $mapped,
							"+mappedFrom" => [
								"cost" => $lastMatch->get("result")->getCost(),
								"score" => $lastMatch->get("result")->getScore(),
								"name"  => $amenity->get("name")
							]
						];
						$model->set("features.[]", $a);
					}
					
				} else {
					$model->set("_debug.unmapped.features.[]", $amenity->get("name"));
				}
				
				
			}
		}
		
		// texts.[]
		if($input->has("descriptions")){
			// Just do English for now. When you change this you will need to create a holding object, because of
			// how Rentivo uses en->propertyDescription, where as Lycan uses propertyDescription[en|fr|de]
			foreach($input->get("descriptions")->getIterator() as $locale => $descriptionCollection){
				// It might be an empty collection.
				if(!$descriptionCollection){
					continue;
				}
				$d = [
					"type" => "PRIMARY_DESCRIPTION",
					"description.$locale" => [
						"content" => $descriptionCollection->get("propertyDescription"),
						"locale" => $locale
					]
				];
				$model->set( "texts.[]", $d);
				
				if($descriptionCollection->has('locationDescription')){
					$d = [
						"type" => "ABOUT_DESTINATION",
						"description.$locale" => [
							"content" => $descriptionCollection->get("locationDescription"),
							"locale" => $locale
						]
					];
					$model->set( "texts.[]", $d);
				}
				
				if($descriptionCollection->has('travelDescription')){
					$d = [
						"type" => "LISTING_DIRECTIONS",
						"description.$locale" => [
							"content" => $descriptionCollection->get("travelDescription"),
							"locale" => $locale
						]
					];
					$model->set( "texts.[]", $d);
				}
				
			}
		}
		
		// policies.[]
		if($input->has("descriptions") ){
			foreach($input->get("descriptions")->getIterator() as $locale => $descriptionCollection){
				// It might be an empty collection.
				if(!$descriptionCollection){
					continue;
				}
				
				if($descriptionCollection->has('paymentDescription')){
					
					$d = [
						"type" => "BOOKING_TERMS",
						"description.$locale" => [
							"content" => $descriptionCollection->get("paymentDescription"),
							"locale" => $locale
						]
					];
					$model->set( "policies.[]", $d);
					
				}
			}
			
		}
		
		// features[suitability]
		if($input->has("suitability")){
			//
			$mapper = new Mapping( 'Pristine\Enums\Suitability', new Mapper\Suitability() );
			$mapper->setTolerance(1);
			foreach($input->get("suitability")->getIterator() as $suitability){
				// suitableSmoking_1
				$enum = $mapper->map( $suitability->get("type") . "_" . $suitability->get("value") );
				if($enum) {
					$s = [
						"category" => "Suitability",
						"type"     => $enum
					];
					$model->set("features.[]", $s);
				} else {
					$model->set("_debug.unmapped.suitability.[]", $suitability->get("type") . "_" . $suitability->get("value") );
				}
			}
			
		}
		
		// media.[]
		if($input->has("images") && !$input->get("images")->isEmpty()){
		
			foreach($input->get("images")->getIterator() as $index => $image){
				$i = [
					"type" => "URI",
					"category" => "PHOTOS",
					"uri" => $image->get("url"),
					"position" => $image->get("position", $index)
				];
				$model->set("media.[]", $i);
			}
		}
		
		// flags.[]
		$model->set("flags.isActive", $input->get("active"));
		$model->set("flags.isDeleted", $input->get("deleted", false));
		
		// pricing.visual
		if($input->has("pricing") && $input->has("pricing.summary")){
			if($input->get("pricing.summary.pricingLow") && $input->get("pricing.summary.pricingHigh") ){
				
				$v = [
					"currency"    => $input->get("pricing.summary.currency"),
					"nightlyLow"  => $input->get("pricing.summary.pricingLow"),
					"nightlyHigh" => $input->get("pricing.summary.pricingHigh")
				];
				$model->set("pricing.visual", $v);
			}
		}
		
		
		
				
		return $model;
	}
	
	public function dtf($d){
		if(is_null($d)){
			return null;
		}
		$date = new \DateTime($d);
		return $date->format("Y-m-d\TH:i:s\Z");
	}
}