<?php

namespace AppBundle\Entity\EventListener;


use AppBundle\Entity\ChannelBrand;
use AppBundle\Entity\Listing;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use AppBundle\Entity\Brand;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Pristine\Schema\Container as SchemaContainer;
class ChannelListener
{
	
	protected $container;
	protected  $channelsUpdated = [];
	public function __construct(ContainerInterface $container) // this is @service_container
	{
		$this->container = $container;
	}
	
	
	public function setContainer(ContainerInterface $container){
		$this->container = $container;
	}
	
	public function preUpdate(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		$channelsUpdated = [];
		
		if($entity instanceof ChannelBrand){
			// We need to find the users now that have been added to the
			$channelsUpdated[] = $entity;
		}
		
		$this->channelsUpdated = $channelsUpdated;
		
	}

	public function prePersist(LifecycleEventArgs $args){
		$entity = $args->getEntity();
		$channelsUpdated = [];
		
		if($entity instanceof ChannelBrand){
			// We need to find the users now that have been added to the
			$channelsUpdated[] = $entity;
		}
		$this->channelsUpdated = $channelsUpdated;
	}
	
	
	public function postFlush(PostFlushEventArgs $args)
	{
		$em = $args->getEntityManager();
		$process = $this->channelsUpdated;
		$this->channelsUpdated = [];
		foreach($process as $channel){
			
			// We need to now CREATE a listing for every item
			// How do we do this? We need to get the brand, then find all rentals in the brand
			if($channel->getBrand() && $channel->getBrand()->getProperties()->count() > 0){
				
				// Get all listings which DO NOT have a property listing created for it.
				$properties = $em->getRepository("AppBundle:Property")->findPropertiesWithoutListingForProviderWithinBrand( $channel->getProvider(), $channel->getBrand() );
				
				if($properties){
					// We can't process any more than 100 rentals at a time, otherwise things get sloowwww.
					
					
					if(count( $properties ) >= 100 ) {
						$this->container->get("request")
							->getSession()
							->getFlashBag()
							->add("warning", "There are more than 100 rentals currently without a corresponding listing record in this channel. The respective listings will be automatically created on the first push.");
						
						$properties = array_slice($properties, 0, 30);
					}
					
					foreach($properties as $property){
						$channelListing = new Listing();
						$channelListing->setChannel($channel);
						$channelListing->setProvider($channel->getProvider());
						$channelListing->setSchemaObject($property->getSchemaObject());
						$channelListing->setMaster($property);
						$channelListing->setDescriptiveName($property->getDescriptiveName());
						// TODO - How can you use the parent?
						$channelListing->setIsSchemaValid($property->getIsSchemaValid());
						$channelListing->setArePoliciesValid(false);
						
						$em->persist($channelListing);
						$em->persist($property);
						
					}
					
					$em->flush();
				}
			}
		}
		
		
	}
}