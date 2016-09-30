<?php

namespace AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Application\Sonata\UserBundle\Entity\User as User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
class UserUpdateListener
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
		
		// False check is compulsory otherwise duplication occurs
		if (($entity instanceof Application\Sonata\UserBundle\Entity\User) === false) {
			if ($args->hasChangedField('username')) {
				$aclProvider = $this->container->get('security.acl.provider');
				
				$oldUsername = $args->getOldValue ('username');
				$user        = $args->getEntity();
				
				$aclProvider->updateUserSecurityIdentity(UserSecurityIdentity::fromAccount($user) , $oldUsername);
			}
		}
	}
	
	
	public function postFlush(PostFlushEventArgs $args)
	{
		
	}
}