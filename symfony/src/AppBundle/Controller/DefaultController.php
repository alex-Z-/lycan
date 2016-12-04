<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Listing;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use React\Promise\Deferred;
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
	
		
		$message = ["id" => 'C068A_CC', "provider" => 1];
		$this->em = $this->container->get("doctrine")->getEntityManager();
		
	
		$providerId = $message['provider'];
		$provider = $this->em->getRepository('\Lycan\Providers\CoreBundle\Entity\ProviderAuthBase')->find($providerId);
		
		$providerKey = strtolower( $provider->getProviderName() );
		$client  = $this->container->get('lycan.provider.api.factory')->create($providerKey, $provider);
		$manager = $this->container->get('lycan.provider.manager.factory')->create($providerKey);
	
		// Get Listing
		// Get the Mapping Definitions
		// Import
	
		$message = [
			"id"	 => "67c2d621-4671-4d30-987d-a10326291796",
			"provider" => 2
		];
	
	
		$providerId = $message['provider'];
		$provider = $this->em->getRepository('\Lycan\Providers\CoreBundle\Entity\ProviderAuthBase')->find($providerId);
		
		$providerKey = strtolower( $provider->getProviderName() );
		$client  = $this->container->get('lycan.provider.api.factory')->create($providerKey, $provider);
		$manager = $this->container->get('lycan.provider.manager.factory')->create($providerKey);
		$manager->setClient($client);
		// Get Listing from Lycan
	
		$listing = $this->em->getRepository("AppBundle:Property")->find($message['id']);
		
		if(!$listing->getIsSchemaValid()){
			$batchLogger->warning("Schema is not valid. Cannot export and syncronization to external channel.", $message);
			return true;
		}
		$schema = $listing->getSchemaObject();
		
		// $schemaContainer = new Container(json_decode( $schema, true));
		// $schemaContainer->fromArray();
	
		$deferred = new Deferred();
		$deferred->resolve($schema);
		
		// This is where things get tricky.
		// A provider might be like Rentivo and can recieve the property in a SINGLE request.
		// Although this is unlikely. We may need to create an abstract "TRANSPORT" object which envelops a batch job process.
		// Something like "create" property. Get ID. Push images. Push descriptions. etc.
		// We'll refactor when we know how other systems do it.
		$deferred->promise()
			->then(function($schema) use ($manager, $listing){
				// If we can pass on credentials. Do a mixin before passing on.
				if($listing->getProvider()->getPassOnCredentials()){
					// If passthrough provider is set. We can receive this in the process outgoing.
					$manager->setPassThroughProvider($listing->getProvider());
				}
				return $schema;
			})
			->then($manager->getProcessOutgoingMappingClosure())
			->then(function($model) use ($manager, $listing, $provider){
				// This will insert/update the lycan model
				$listings = $this->em->getRepository("AppBundle:Property")->findListingsByProvider($provider, $listing);
				// We're assuming we only have a single channel listing for now, but this might change. This will need refactoring.
				$channelListing =  ($listings && $listings->count() >= 1) ? $listings->current() : null;
				// If we know what the CHANNEL LISTING is, we pass that to the upsert. Because then we can UPDATE. Rather than insert.
				
				$id = $manager->upsert($model, $channelListing);
				// If NOT NULL
				
				if($id){
					try {
						// Now we create a child listing.
						$channelListing = $channelListing?: new Listing();
						$channelListing->setProvider($provider);
						$channelListing->setProviderListingId($id);
						$channelListing->setSchemaObject($model->toJson());
						$channelListing->setMaster($listing);
						$channelListing->setDescriptiveName($model->get("name"));
						$channelListing->setIsSchemaValid($listing->getIsSchemaValid());
						$this->em->persist($channelListing);
						$this->em->persist($listing);
						$this->em->flush();
					} catch (\Exception $e){
						
					}
					
				}
				
			});
		
		
		

		die("Test Index Homepage: " . uniqid());
		// $this->render("AppBundle::custom_layout.html.twig");
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }
}
