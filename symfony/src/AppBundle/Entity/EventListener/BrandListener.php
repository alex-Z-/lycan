<?php

namespace AppBundle\Entity\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use AppBundle\Entity\Brand;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
class BrandListener
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
		$em = $args->getEntityManager();
		
		
				
		
	}
	
	
	public function postFlush(PostFlushEventArgs $args)
	{
		$em = $args->getEntityManager();
		$uow = $em->getUnitOfWork();
		
		$brandsUpdated = [];
		
		foreach ($uow->getScheduledCollectionDeletions() as $col) {
			$brands = $col->toArray();
			// Obvious THIS could be ANY Entity.. We still gotta check
			foreach($brands as $brand){
				if($brand instanceof Brand){
					// We need to find the users now that have been added to the
					$brandsUpdated[] = $brand;
				}
			}
		}
		
		
		foreach ($uow->getScheduledCollectionUpdates() as $col) {
			$brands = $col->toArray();
			// Obvious THIS could be ANY Entity.. We still gotta check
			foreach($brands as $brand){
				if($brand instanceof Brand){
					// We need to find the users now that have been added to the
					$brandsUpdated[] = $brand;
				}
			}
		
		}
		
		if(!empty($brandsUpdated)){
			foreach($brandsUpdated as $brand){
				
				$manager = $this->container->get('oneup_acl.manager');
				// $manager->revokeAllObjectPermissions($brand);
				foreach($brand->getMembers() as $user){
					// Adds a permission no matter what other permissions existed before
					try {
						$manager->setObjectPermission($brand, MaskBuilder::MASK_VIEW, $user);
					} catch(\Exception $e){
						
					}
					
				}
				
			
				
			}
		}
		
	}
}