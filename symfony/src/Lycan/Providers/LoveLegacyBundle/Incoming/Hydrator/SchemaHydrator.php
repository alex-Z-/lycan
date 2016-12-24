<?php
namespace Lycan\Providers\LoveLegacyBundle\Incoming\Hydrator;
use Incoming;
use Pristine\Schema\Container as SchemaContainer;
use Pristine\Enums;
use Lycan\Providers\LoveLegacyBundle\Incoming\Mapper;
use Pristine\Mapper\Mapping;
use Pristine\Utils\LocaleUtils;
use HTMLPurifier_Config as Config;
use HTMLPurifier;
class SchemaHydrator implements Incoming\Hydrator\HydratorInterface
{
	
	protected $container;
	public function __construct($container)
	{
		$this->container = $container;
	}
	
	public function hydrate( $input, $model)
	{
		
		$model->set('$id', $input->get("id"));
		$model->set('$brand', $input->get("ownerCode"));
		$model->set('$schemaBuiltAt', $this->dtf( ));
		$model->set('$createdAt', $this->dtf( ) );
		$model->set('$lastModifiedAt', $this->dtf() );
		$model->set('$locale', "en_GB");
		$model->set('name', $input->get('name'));
		
		$mapper = new Mapping( 'Pristine\Enums\ListingTypes', new Mapper\PropertyTypes() );
		$model->set('listing.type', $mapper->map( "Cottage" ) );
		$model->set('listing.arrangement', "ENTIRE_LISTING");
		$model->set('listing.bedrooms', $input->get("bedrooms"));
		$model->set('listing.bathrooms', $input->get("attributes.Bathrooms", null ));
		$model->set('listing.maxOccupancy', $input->get("accommodates")); // Additionals with extra beds
		$model->set('listing.sleeps', $input->get("accommodates")); // Total comfortable sleeps..
	
		$model->set('location.latitude', $input->get('coordinates.latitude'));
		$model->set('location.longitude', $input->get('coordinates.longitude'));
		
		$model->set("address.addressLine1", $input->get("address.addr1"));
		$model->set("address.addressLine2", $input->get("address.addr2"));
		$model->set("address.city", $input->get("address.town"));
		$model->set("address.stateProvince", $input->get("address.county"));
		$model->set("address.countryISO2", $input->get("address.country", "UK"));
		$model->set("address.zipPostCode", $input->get("address.postcode"));
		
		$locale = "en_GB";
		
		$brandCode = $input->get("brandCode");
	
		$description = $input->get( sprintf( "brands.%s.description", $brandCode ));
		
		try {
			$config =  Config::createDefault();
			$config->set('HTML.Allowed', 'p, b, blockquote, hr'); // Allow Nothing
			// $config->set('AutoFormat.AutoParagraph', true);
			$config->set('AutoFormat.RemoveEmpty', true);
			$config->set('AutoFormat.RemoveEmpty.RemoveNbsp', true);
			
			$purifier   = new HTMLPurifier($config);
			$description = $purifier->purify($description);
		} catch(\Exception $e){
			
		}
	
		$d = [
			"type" => "PRIMARY_DESCRIPTION",
			"description.$locale" => [
				"content" => $description,
				"locale" => $locale
			]
		];
		$model->set( "texts.[]", $d);
		
		
		// media.[]
		if($input->has("images") && !$input->get("images")->isEmpty()){
			
			foreach($input->get("images")->getIterator() as $index => $image){
				
				$i = [
					"type" => "URI",
					"category" => "PHOTOS",
					"uri" => str_replace(" ", "%20", $image->get("url")),
					"position" => $image->get("position", $index)
				];
				$model->set("media.[]", $i);
			}
		}
		
		if($input->has("pets") && $input->get("pets")){
			$a = [
				"category"    => "Amenities",
				"type"        => Enums\Features::GENERAL_PET_FRIENDLY,
			];
			$model->set("features.[]", $a);
		}
		
		if($input->has("wifi") && $input->get("wifi")){
			$a = [
				"category"    => "Amenities",
				"type"        => Enums\Features::COMMUNICATION_INTERNET_WIFI,
			];
			$model->set("features.[]", $a);
		}
		
		// features.[]
		if($input->has("attributes")){
			$mapper = new Mapping( 'Pristine\Enums\Features', new Mapper\Features() );
			$mapper->setTolerance(0.9);
			foreach($input->get("attributes")->getIterator() as $amenity => $value){
				// Only continue if true..
				if($value === false){
					continue;
				}
				
				$enum = $mapper->map( $amenity );
				$lastMatch = $mapper->getLastMatch();
				
				if(is_string($enum)) {
					$a = [
						"category"    => "Amenities",
						"type"        => $enum,
						"+mappedFrom" => [
							"cost" => $lastMatch->get("result")->getCost(),
							"score" => $lastMatch->get("result")->getScore(),
							"name"  => $amenity
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
								"name"  => $amenity
							]
						];
						$model->set("features.[]", $a);
					}
					
				} else {
					// For example PEts.
					if(is_numeric($value)){
						$amenity = $amenity . ": " . $value;
					}
					
					$model->set("_debug.unmapped.features.[]", $amenity );
					$this->container->get("app.logger.missing")->debug($amenity, ["LoveLegacy"], 1);
					
				}
				
				
			}
		}
		
		// Get low price
		// Get high price
		// Map and find all available days
		// Map and fine all changeovers
		
		// dump($input->toArray());die();
		
		
		// Pricing Visual
		$weeklyLow = null;
		$weeklyHigh = null;
		
		// We don't want pricing days to exist. Some providers mistakenly return a single result.
		if($input->has("pricing") && !$input->has("pricing.days")) {
			
			$input->get("pricing")
				->forAll(function ($key, $item) use (&$weeklyLow, &$weeklyHigh) {
					
					$weeklyLow  = ((float)$item->get("price") < $weeklyLow) || is_null($weeklyLow) ? (float)$item->get("price") : $weeklyLow;
					$weeklyHigh = ((float)$item->get("price") > $weeklyHigh) || is_null($weeklyHigh) ? (float)$item->get("price") : $weeklyHigh;
					
					return true;
				});
			
			$model->set("pricing.visual.currency", "GBP");
			$model->set("pricing.visual.weeklyLow", $weeklyLow);
			$model->set("pricing.visual.weeklyHigh", $weeklyHigh);
			
			// Sort out the available days. This can be done by finding TODAY day. Then the final date in the available breaks.
			// Create a stringfill of NNNNNNNNN, then looping over each day and setting Y if the date season is available.
			$today    = date("Y-m-d");
			$lastDate = date("Y-m-d");
			
			foreach ($input->get("pricing")
						 ->getIterator() as $range) {
				if ($range->get("todate") > $lastDate) {
					$lastDate = $range->get("todate");
				}
			}
			
			$daysDifference = (strtotime($lastDate) - strtotime($today)) / (60 * 60 * 24);
			if($daysDifference === 0){
				$daysDifference = 1;
			}
			$startDatum     = $today;
			$sequence       = implode(array_fill(0, $daysDifference, "N"));
			// Now we overwrite each item in the BS
			foreach ($input->get("pricing")
						 ->getIterator() as $range) {
				
				// Get the days difference from the startDatum.
				$daysDifference = (strtotime($range->get("fromdate")) - strtotime($startDatum)) / (60 * 60 * 24);
				$length         = (int)$range->get("days");
				$sequence       = substr_replace($sequence, implode(array_fill(0, $length, "Y")), $daysDifference);
			}
			$model->set("+availability.startDatum", $startDatum);
			$model->set("+availability.sequence", $sequence);
		} else {
			
		}
		
		// The Calendar Mapping for Tabs is interesting.. Need to find the lowest date..  and start from there...
	
		return $model;
	}
	
	
	public function dtf($d = null){
		if(is_null($d)){
			return null;
		}
		$date = new \DateTime($d);
		return $date->format("Y-m-d\TH:i:s\Z");
	}
	
	
}