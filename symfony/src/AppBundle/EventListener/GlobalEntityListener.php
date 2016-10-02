<?php

namespace AppBundle\EventListener;

use AppBundle\AppBundle;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;

use Application\Sonata\UserBundle\Entity\User as User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
class GlobalEntityListener
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
		
		if (method_exists($entity, "setOwner") && method_exists($entity, "getOwner")) {
			$securityContext = $this->container->get('security.authorization_checker');
			if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
				if ($entity->getOwner() === null) {
					$user = $this->container->get('security.context')->getToken()->getUser();
					if ($user) {
						$entity->setOwner($user);
					}
				}
			}
		}
		
	}
	
	
	public function postFlush(PostFlushEventArgs $args)
	{
		
	}
}