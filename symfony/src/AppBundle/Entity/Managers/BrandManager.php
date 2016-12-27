<?php

namespace AppBundle\Entity\Managers;

use AppBundle\Entity\Brand;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
class BrandManager
{
	protected $container;
	
	public function setContainer(ContainerInterface $container){
		$this->container = $container;
	}
	
	public function refreshACE(Brand $brand){
		
		$manager = $this->container->get('oneup_acl.manager');
		// it's not existing yet.. why refresh?
		if(!$brand->getId()){
			return;
		}
	
		if ( method_exists($brand, "setOwner") && method_exists($brand, "getOwner")) {
		
			$user = $this->container->get('security.context')->getToken()->getUser();
			if ($brand->getOwner() !== $user) {
				
				$manager->revokeAllObjectPermissions($brand);
				$manager->setObjectPermission($brand, MaskBuilder::MASK_OWNER, $brand->getOwner() );
				
				// We also need to update the "members" if it's a brand...
				foreach($brand->getMembers() as $member){
					
					$manager->addObjectPermission($brand, MaskBuilder::MASK_VIEW, $member->getMember() );
				}
				
			}
			
			// Once complete.. get all Registrys and create ACEs
			foreach( $brand->getMembers() as $member ){
				if(!$member->getId()){
					continue;
				}
				$aclProvider = $this->container->get('security.acl.provider');
				// Member needs to exist, otherwise you can't create ACE...
				$manager->revokeAllObjectPermissions($member);
				if($member && $member->getId() ) {
					// $aclProvider->createAcl(ObjectIdentity::fromDomainObject($member));
					$objectIdentity = ObjectIdentity::fromDomainObject($member);
					try {
						$acl = $aclProvider->findAcl($objectIdentity);
					} catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
						$acl = $aclProvider->createAcl($objectIdentity);
					}
					
				
					if ($aclProvider->findAcl($objectIdentity)) {
						
						$manager->addObjectPermission($member, MaskBuilder::MASK_OWNER, $brand->getOwner());
						$manager->addObjectPermission($member, MaskBuilder::MASK_VIEW, $member->getMember());
					} else {
						// die("NO ACL ENTRY EXISTS FOR BRAND REGISTRY");
					}
				}
			}
		}
		
		
		$members = $brand->getMembers();
		foreach($members as $member){
			
		}
		
	}
}
