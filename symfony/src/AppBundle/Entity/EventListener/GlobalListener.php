<?php

namespace AppBundle\Entity\EventListener;

use AppBundle\AppBundle;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;

use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Application\Sonata\UserBundle\Entity\User as User;
use AppBundle\Entity\Brand;
use AppBundle\Entity\UserBrandRegistry;
use AppBundle\Entity\Managers\BrandManager;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;


use Symfony\Component\DependencyInjection\ContainerInterface;

class GlobalListener
{
	
	protected $container;
	
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
	
		
	}

	public function prePersist(LifecycleEventArgs $args){
		
		$entity = $args->getEntity();
		
		if ( php_sapi_name() !== "cli" && method_exists($entity, "setOwner") && method_exists($entity, "getOwner")) {
			$securityContext = $this->container->get('security.authorization_checker');
			if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
				$user = $this->container->get('security.context')->getToken()->getUser();
				if ($entity->getOwner() === null) {
					if ($user) {
						$entity->setOwner($user);
					}
				}
			}
		}
		
	}
	
	private function _updateACE($entity, $uow = null){
		$manager = $this->container->get('oneup_acl.manager');
		
		if ( method_exists($entity, "setOwner") && method_exists($entity, "getOwner") && $entity->getOwner() && $entity->getId() ) {
			// ONLY RUN THIS.. IF THE OBJECT ALREADY EXISTS???
			if($uow) {
				$uow->computeChangeSets(); // do not compute changes if inside a listener
				$changeset = $uow->getEntityChangeSet($entity);
				
				$manager = $this->container->get('oneup_acl.manager');
				$aclProvider = $this->container->get('security.acl.provider');
				
				try {
					$objectIdentity = ObjectIdentity::fromDomainObject($entity);
					if(isset($changeset['owner'])) {
						// Maybe we should not Revoke???
						$manager->revokeAllObjectPermissions($entity);
						$manager->setObjectPermission($entity, MaskBuilder::MASK_OWNER, $entity->getOwner());
					}
				} catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
				}
					
			}
		}
		
		if($entity instanceof Brand){
			$brandManager = new BrandManager();
			$brandManager->setContainer($this->container);
			$brandManager->refreshACE($entity);
		}
		
		if($entity instanceof UserBrandRegistry && $entity->getBrand()){
			$brandManager = new BrandManager();
			$brandManager->setContainer($this->container);
		
			// Get brand
			$brandManager->refreshACE( $entity->getBrand() );
			
		}
		
		
		
		
		
	}
	
	public function onFlush(OnFlushEventArgs $args)
	{
		$em = $args->getEntityManager();
		$uow = $em->getUnitOfWork();
		
		if ( php_sapi_name() !== "cli") {
			
		
			
			foreach ($uow->getScheduledEntityInsertions() as $entity) {
								
				$this->_updateACE($entity, $uow);
			}
			
			foreach ($uow->getScheduledEntityUpdates() as $entity) {
				
				$this->_updateACE($entity, $uow);
			}
			
			foreach ($uow->getScheduledEntityDeletions() as $entity) {
				$this->_updateACE($entity, $uow);
			}
			
			foreach ($uow->getScheduledCollectionDeletions() as $col) {
				
			}
			
			foreach ($uow->getScheduledCollectionUpdates() as $col) {
				
			}
		}
		
		
		
	}
}