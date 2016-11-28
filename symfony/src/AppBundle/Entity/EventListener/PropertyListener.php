<?php

namespace AppBundle\Entity\EventListener;


use AppBundle\Entity\Property;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;


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
	
	public function preUpdate(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		$em = $args->getEntityManager();
		$uow = $args->getEntityManager()->getUnitOfWork();
		// False check is compulsory otherwise duplication occurs
		if($entity instanceof Property){
			if($uow) {
				$uow->computeChangeSets(); // do not compute changes if inside a listener
				$changeset = $uow->getEntityChangeSet($entity);
				if ( (isset($changeset['syncedAt']) && !isset($changeset['brands'])) && $entity->getProvider() && $entity->getProvider()->getAutoMappedToBrands()) {
					// Need to add the property to the auto mapped brands if it is NOT already.
					foreach ($entity->getProvider()->getAutoMappedToBrands() as $brand) {
						$entity->addBrand($em->getReference("AppBundle:Brand", $brand));
						$em->persist($entity);
						$this->_queuedToFlush[] = $entity;
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