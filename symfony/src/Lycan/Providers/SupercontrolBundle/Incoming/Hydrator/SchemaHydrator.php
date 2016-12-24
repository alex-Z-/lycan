<?php
namespace Lycan\Providers\SupercontrolBundle\Incoming\Hydrator;
use Incoming;
use Pristine\Schema\Container as SchemaContainer;
use Pristine\Enums;
use Lycan\Providers\SupercontrolBundle\Incoming\Mapper;
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
		
		$model->configure(["allowNull" => false, "autoCascadeOptions" => true]);
		
		$model->set('$id', $input->get("propertycode"));
		$model->set('$schemaBuiltAt', $this->dtf( ));
		$model->set('$createdAt', $this->dtf( ) );
		$model->set('$lastModifiedAt', $this->dtf() );
		$model->set('$locale', "en_GB");
		$model->set('name', $input->get('propertyname'));
		
		$mapper = new Mapping( 'Pristine\Enums\ListingTypes', new Mapper\PropertyTypes() );
		$model->set('listing.type', $mapper->map( "Cottage" ) );
		$model->set('listing.arrangement', "ENTIRE_LISTING");

		$model->set('listing.bedrooms', $input->get("bedrooms_new"));
		$model->set('listing.bathrooms', $input->get("bathrooms_new", null ));
		$model->set('listing.maxOccupancy', $input->get("sleeps")); // Additionals with extra beds
		$model->set('listing.sleeps', $input->get("sleeps")); // Total comfortable sleeps..
		
		$model->set('location.latitude', $input->get('latitude'));
		$model->set('location.longitude', $input->get('longitude'));
		
		$model->set("address.addressLine1", $input->get("propertyaddress"));
	
		if( $input->get("regionname.$") ) {
			$model->set("address.stateProvince", $input->get("regionname.$"));
		}
		
		$model->set("address.countryISO2", $input->get("countryiso"));
		$model->set("address.zipPostCode", $input->get("propertypostcode"));
		
		$locale = "en_GB";
		$description = $input->get(  "webdescription");
		
		try {
			$config =  Config::createDefault();
			$config->set('HTML.Allowed', 'p, b, ul, li, blockquote, hr'); // Allow Nothing
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
		if($input->has("images.property.ImageCollection") && !$input->get("images.property.ImageCollection")->isEmpty()){
			
			foreach($input->get("images.property.ImageCollection")->getIterator() as $index => $image){
				// TODO - add caption.
				
				$i = [
					"type" => "URI",
					"category" => "PHOTOS",
					"uri" => $image->get("Main"),
					"position" => $image->get("position", $index)
				];
				$model->set("media.[]", $i);
			}
		}
		
		if($input->has("propertyurl")){
			if (preg_match("#https?://#", $input->get("propertyurl")) === 0) {
				$uri = 'http://'. $input->get("propertyurl");
			} else {
				$uri = $input->get("propertyurl");
			}
			
			$model->set("media.[]", [
				"type" => "URI",
				"category" => "LINK",
				"uri" => $uri,
				"isListingPage" => true
			]);
		}
		
	
		// features.[]
		if($input->has("variables.varcat")){
			$mapper = new Mapping( 'Pristine\Enums\Features', new Mapper\Features() );
			$mapper->setTolerance(0.95);
		
			foreach($input->get("variables.varcat")->getIterator() as  $cats) {
				foreach ($cats->get("varcatitems")->current()->getIterator() as $items) {
				
					foreach($items->getIterator() as $item) {
					
						// Only continue
						// if true..
					
						$enum      = $mapper->map($item->get("variable.$"));
						$lastMatch = $mapper->getLastMatch();
						
						if (is_string($enum)) {
							
							
							$a = [
								"category"    => "Amenities",
								"type"        => $enum,
								"+mappedFrom" => [
									"cost"  => $lastMatch->get("result")
										->getCost(),
									"score" => $lastMatch->get("result")
										->getScore(),
									"name"  => $item->get("variable.$")
								]
							];
							
							$model->set("features.[]", $a);
							
							
						} elseif (is_array($enum)) {
							
							foreach ($enum as $mapped) {
								$a = [
									"category"    => "Amenities",
									"type"        => $mapped,
									"+mappedFrom" => [
										"cost"  => $lastMatch->get("result")
											->getCost(),
										"score" => $lastMatch->get("result")
											->getScore(),
										"name"  => $item->get("variable.$")
									]
								];
								$model->set("features.[]", $a);
							}
							
						} else {
							
							// We want to write the amenity to a file.
							$this->container->get("app.logger.missing")
								->debug($item->get("variable.$"), ["Supercontrol"], 1);
							
							$model->set("_debug.unmapped.features.[]", $item->get("variable.$"));
						}
					}
					
					
				}
			}
		}
		
		if($input->has("availability.BookedStays.BookedStay")){
			// Start Datum
			$ranges = $this->getAvailabilityRange(  $input->get("availability.BookedStays.BookedStay")->toArray() );
			$model->set("+availability", $ranges);
		}
		
		// Pricing Visual
		$weeklyLow = null;
		$weeklyHigh = null;
		if($input->has("maxprice")){
			$model->set("pricing.visual.currency", $input->get("maxprice.@currency"));
			$model->set("pricing.visual.weeklyHigh", $input->get("maxprice.$"));
		}
		
		if($input->has("minprice")){
			$model->set("pricing.visual.currency", $input->get("minprice.@currency"));
			$model->set("pricing.visual.weeklyLow", $input->get("minprice.$"));
		}
		
		return $model;
	}
	
	public function getAvailabilityRange($ranges, $addDaysToDuration = 0)
	{
		$minDate = null;
		$maxDate = null;
		$startDatum = new \DateTime(); // Today
		// First get the min date.. this acts as the datum...
		
		foreach ($ranges as $bookings) {
			$minDate = ($bookings['ArrivalDate'] < $minDate || is_null($minDate)) ? $bookings['ArrivalDate'] : $minDate;
			$maxDate = ($bookings['DepartureDate'] > $maxDate || is_null($maxDate)) ? $bookings['DepartureDate'] : $maxDate;
		}
		
		if (!$minDate instanceof \DateTime) {
			$s = new \DateTime($minDate);
			$s->setTime(0, 0, 0);
		} else {
			$s = $minDate->setTime(0, 0, 0);
		}
		// max date
		$md = $maxDate instanceof \DateTime ? $maxDate : new \DateTime($maxDate);
		// THIS IS THE PROBLEM!!!! FINALLY. You need to know how many MIDNIGHTS have passed. That is the difference.
		// Currently, you are just checking the number of 24 hours.
		$md->setTime(0, 0);
		$difference = $s->diff($md)->format('%a');
		
		// If zero difference.. return immediately
		if ($difference > 0) {
			$stretch = array_fill(0, $difference + 1, 0);
		} else {
			return [
				"startDatum"   => $s->format("Y-m-d\TH:i:s\Z"),
				"sequence" => null
			];
		}
		
		// Second Pass
		foreach ($ranges as $event) {
			$sd = is_a($event['ArrivalDate'], "DateTime") ? $event['ArrivalDate'] : new \DateTime($event['ArrivalDate']);
			$ed = is_a($event['DepartureDate'], "DateTime") ? $event['DepartureDate'] : new \DateTime($event['DepartureDate']);
			
			// FINAL FIX (3) -> EVERYTRHING BELOW HERE WAS WRONG.
			// YOU NEED TO SET THE DATE TIMES TO 0 0 ....
			// http://stackoverflow.com/questions/5215190/calculating-how-many-midnights-is-one-date-past-another-in-php
			$sd->setTime(0, 0);
			$ed->setTime(0, 0);
			// NEWFIX (WRONG AGAIN): Adding one day was wrong. I've removed it....
			// WRONG-> We have to add one day, because of our the days, are only full days
			// WRONG-> And we are actually staying 2 nights, (becayuse of the hours)
			// WRONG-> FIX: ALWAYS ADD 1 DAY AT LEAST ...
			$duration = $sd->diff($ed)->format('%a') + $addDaysToDuration;
			$offset = $s->diff($sd)->format('%a');
			// Don't know why duration would not be positive.. but was getting errors.... so meh.
			if ($duration <= 0) {
				$bs = "";
			} else {
				if(isset($event['bookingType']) && $event['bookingType'] === -1 ){
					$bs = array_fill(0, $duration, "Q");
				} else {
					$bs = array_fill(0, $duration, 1);
				}
				
			}
			
			array_splice($stretch, $offset, $duration, $bs);
		}
		
		$stringStretch = str_replace("1", "N", str_replace("0", "Y", implode($stretch)));
		
		return [
			"startDatum"   => $s->format("Y-m-d\TH:i:s\Z"),
			"sequence" => $stringStretch
		];
	}
	
	
	public function dtf($d = null){
		if(is_null($d)){
			return null;
		}
		$date = new \DateTime($d);
		return $date->format("Y-m-d\TH:i:s\Z");
	}
	
	
}