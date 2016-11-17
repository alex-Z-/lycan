<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
	
		
		$message = ["id" => 'VI05DU_DI', "provider" => 2];
		$this->em = $this->container->get("doctrine")->getEntityManager();
		
	
		$providerId = $message['provider'];
		$provider = $this->em->getRepository('\Lycan\Providers\CoreBundle\Entity\ProviderAuthBase')->find($providerId);
		
		$providerKey = strtolower( $provider->getProviderName() );
		$client  = $this->container->get('lycan.provider.api.factory')->create($providerKey, $provider);
		$manager = $this->container->get('lycan.provider.manager.factory')->create($providerKey);
	
		// Get Listing
		// Get the Mapping Definitions
		// Import
		$client ->getListingFull($message['id'])
			->then($manager->getProcessMappingClosure())
			->then(function($schema) use ($provider) {
				// Get Lycan Importer
				$lycan = $this->container->get("app.importer");
				// We want the import function to do the checks.
				// This avoids duplication around lots of code.
				$property = $lycan->import( $schema, $provider );
				return $property;
			} )
			->then( function($property) use ($eventGroup) {
				// We should now attempt to claim all logs with the current eventGroup...
				// And assign them to this property..
			
			})->then( function() use ($message, $provider){
				
			});
		
		
		
		die("Test Index Homepage");
		// $this->render("AppBundle::custom_layout.html.twig");
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }
}
