<?php

namespace AppBundle\Entity\EventListener;


use AppBundle\Entity\Listing;
use AppBundle\Entity\Property;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;

use JsonSchema\Constraints\Factory;
use JsonSchema\Constraints\Constraint;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
class PropertyListener
{
	
	protected $container;
	protected $_queuedToFlush;
	public function __construct(ContainerInterface $container) // this is @service_container
	{
		$this->container = $container;
	}
	
	
	public function setContainer(ContainerInterface $container){
		$this->container = $container;
	}
	
	public function prePersist(LifecycleEventArgs $args){
		$entity = $args->getEntity();
		$em = $args->getEntityManager();
		$uow = $args->getEntityManager()->getUnitOfWork();
	
		if($entity instanceof Listing){
			// We get the provider. Then the policies
			$this->_checkPolicies($entity, $em);
			
		}
		if($entity instanceof Property) {
			if($entity->getProvider()) {
				foreach ($entity->getProvider()
							 ->getAutoMappedToBrands() as $brand) {
					if (!$entity->getBrands()
						->contains($em->getReference("AppBundle:Brand", $brand))
					) {
						$entity->addBrand($em->getReference("AppBundle:Brand", $brand));
						// We need to know if we have to auto create a listing because the property exists on a mapped brand.
						$brand = $em->getRepository('AppBundle:Brand')->find($brand);
						if($brand->getChannels()->count() > 0){
							foreach($brand->getChannels() as $channel){
								try {
									$channelListing = new Listing();
									$channelListing->setChannel($channel);
									$channelListing->setProvider($channel->getProvider());
									$channelListing->setSchemaObject($entity->getSchemaObject());
									$channelListing->setMaster($entity);
									$channelListing->setDescriptiveName($entity->getDescriptiveName());
									$channelListing->setIsSchemaValid($entity->getIsSchemaValid());
									$em->persist($channelListing);
								
								} catch (\Exception $e){
									
								}
							}
						}
						
					}
				}
			}
			
		}
	}
	
	private function _checkPolicies(Listing $entity, $em){
		
		if($entity->getProvider()->getPolicies()->count() > 0) {
			$policies = $entity->getProvider()->getPolicies();
			$validated = true;
			foreach($policies as $policy){
				$schemaDefinition = json_decode( $policy->getPolicy()->getPolicySchema() );
				// Provide $schemaStorage to the Validator so that references can be resolved during validation
				$schemaStorage = new SchemaStorage();
				$schemaStorage->addSchema('file://list-schema', $schemaDefinition);
				$validator = new Validator(new Factory(
					$schemaStorage,
					null,
					Constraint::CHECK_MODE_TYPE_CAST
				));
				$validator->check(   json_decode( $entity->getSchemaObject() ) ,  $schemaDefinition );
				
				if(!$validator->isValid()){
					$validated = false;
					$errors[] =  $validator->getErrors();
					if($this->container->get("request")) {
						$this->container->get("request")
							->getSession()
							->getFlashBag()
							->add("error", "Schema does not validate against custom policies attached to provider.");
						// $batchLogger->warning(, [ "schema" =>  json_decode( $schema, true ),  "input" => json_decode( $policy->getPolicy()->getPolicySchema(), true ),  "output" => $validator->getErrors() ]);
					}
				}
			}
			
			if($validated){
				$entity->setArePoliciesValid(true);
				$entity->setPoliciesErrors( null );
			} else {
				$entity->setArePoliciesValid(false);
				$entity->setPoliciesErrors( $errors );
			}
			
		} else {
			$entity->setArePoliciesValid(true);
			$entity->setPoliciesErrors(null);
		}
		
	
		
	}
	
	public function preUpdate(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		$em = $args->getEntityManager();
		$uow = $args->getEntityManager()->getUnitOfWork();
		
		
		if($entity instanceof Listing){
			// We get the provider. Then the policies
			$this->_checkPolicies($entity, $em);
			$meta = $em->getClassMetadata(get_class($entity));
			$uow->recomputeSingleEntityChangeSet($meta, $entity);
		}
		
		// False check is compulsory otherwise duplication occurs
		if($entity instanceof Property){
			if($uow) {
				$uow->computeChangeSets(); // do not compute changes if inside a listener
				$changeset = $uow->getEntityChangeSet($entity);
				
				if ( (isset($changeset['syncedAt']) && !isset($changeset['brands'])) && $entity->getProvider() && $entity->getProvider()->getAutoMappedToBrands()) {
					// Need to add the property to the auto mapped brands if it is NOT already.
					foreach ($entity->getProvider()->getAutoMappedToBrands() as $brand) {
						if(!$entity->getBrands()->contains($em->getReference("AppBundle:Brand", $brand))) {
							$entity->addBrand($em->getReference("AppBundle:Brand", $brand));
							$em->persist($entity);
							$this->_queuedToFlush[] = $entity;
						}
					}
				}
				
				// Do we need to automatically cascade updates to sub-listings?
				$listings = $entity->getListings();
				if($listings) {
					foreach ($listings as $listing) {
						$provider = $listing->getProvider();
						if ($provider->getAutoCascadeUpdateFromMaster()) {
							$listing->setSchemaObject($entity->getSchemaObject());
							$this->_queuedToFlush[] = $listing;
						}
					}
				}
			}
		}
		
	}

	
	public function postFlush(PostFlushEventArgs $args)
	{
		$em = $args->getEntityManager();
		if(!empty($this->_queuedToFlush)){
		
			foreach ($this->_queuedToFlush as $property){
				$em->persist($property);
			}
			$this->_queuedToFlush = [];
			$em->flush();
		}
	}
}