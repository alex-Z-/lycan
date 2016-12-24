<?php
namespace Lycan\Providers\TabsBundle\Incoming\Transformer;
use Incoming;
use Pristine\Schema\Container as SchemaContainer;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Client;
class TabsTransformer implements Incoming\Transformer\TransformerInterface
{
	protected $container;
	public function setContainer($container){
		$this->container = $container;
	}
	
	public function transform($input)
	{
		
		 $container = new SchemaContainer(  );
		 $input = $container->fromArray($input);
	
	
		// This is where we can try and fix the post codes, if we do not have some already.
		if($input->get('location.coordinates.latitude') === "0" &&
		   $input->get('location.coordinates.longitude') === "0" &&
		   $input->has("address.postcode")
		){
			$client = new Client([ 'base_uri' => 'https://api.postcodes.io' ]);
			
			
			$postcode = $input->get("address.postcode");
			
			$logger = $this->container->get('app.logger.jobs');
			
			try {
				$response = $client->get('/postcodes/' . str_replace(" ", "", $postcode));
				
				$body = json_decode( (string) $response->getBody(), true);
				if($body && isset($body['result'])){
					$logger->debug("Geocoding Postcode because source data is missing latitude and longitude information.", [ "input" => $postcode, "output" => $body ]);
					$input->set("location.coordinates.latitude", $body['result']['latitude']);
					$input->set("location.coordinates.longitude", $body['result']['longitude']);
				}
				
			} catch (\Exception $e){
				$logger->warning("Geocoding failed for Postcode", [ "input" => $postcode]);
			}
			
			
		}
		
		
	 	 return $input;
	}
}