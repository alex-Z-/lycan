<?php

namespace Application\Sonata\UserBundle\DataFixtures\ORM;

use AppBundle\Entity\Property;
use Application\Sonata\UserBundle\Entity\User;
use Application\Sonata\UserBundle\Entity\Group;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

/**
 * Defines the sample data to load in the database when running the unit and
 * functional tests. Execute this command to load the data:.
 *
 *   $ php app/console doctrine:fixtures:load
 *
 * See http://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html
 *
 * @author MoriorGames <moriorgames@gmail.com>
 */
class LoadFixtures extends AbstractFixture implements FixtureInterface, ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    public function load(ObjectManager $manager)
    {
		$this->loadGroups($manager);
		$this->loadUsers($manager);
		$this->loadProperties($manager);
		
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    

    
	private function loadGroups(ObjectManager $manager)
	{
		$groupAdmin = new Group('LYCAN_OWNER');
		
		
		// a:11:{i:0;s:17:"ROLE_SONATA_ADMIN";i:1;s:6:"SONATA";i:2;s:37:"ROLE_ADMIN_PROPERTY_ADMIN_PERMISSIONS";i:3;s:38:"ROLE_ADMIN_PROPERTY_OBJECT_PERMISSIONS";i:4;s:45:"ROLE_SONATA_USER_ADMIN_USER_ADMIN_PERMISSIONS";i:5;s:46:"ROLE_SONATA_USER_ADMIN_USER_OBJECT_PERMISSIONS";i:6;s:46:"ROLE_SONATA_USER_ADMIN_GROUP_ADMIN_PERMISSIONS";i:7;s:33:"ROLE_SONATA_USER_ADMIN_USER_GUEST";i:8;s:26:"ROLE_ADMIN_PROPERTY_EDITOR";i:9;s:23:"ROLE_ADMIN_BRAND_EDITOR";i:10;s:40:"ROLE_ADMIN_LYCAN_PROVIDER_RENTIVO_EDITOR";}
		$d = unserialize('a:12:{i:0;s:17:"ROLE_SONATA_ADMIN";i:1;s:37:"ROLE_ADMIN_PROPERTY_ADMIN_PERMISSIONS";i:2;s:38:"ROLE_ADMIN_PROPERTY_OBJECT_PERMISSIONS";i:3;s:45:"ROLE_SONATA_USER_ADMIN_USER_ADMIN_PERMISSIONS";i:4;s:46:"ROLE_SONATA_USER_ADMIN_USER_OBJECT_PERMISSIONS";i:5;s:46:"ROLE_SONATA_USER_ADMIN_GROUP_ADMIN_PERMISSIONS";i:6;s:22:"ROLE_ADMIN_BRAND_GUEST";i:7;s:22:"ROLE_ADMIN_BRAND_STAFF";i:8;s:25:"ROLE_ADMIN_PROPERTY_GUEST";i:9;s:25:"ROLE_ADMIN_PROPERTY_STAFF";i:10;s:39:"ROLE_ADMIN_LYCAN_PROVIDER_RENTIVO_GUEST";i:11;s:39:"ROLE_ADMIN_LYCAN_PROVIDER_RENTIVO_STAFF";}');
		foreach ($d as $role) {
			$groupAdmin->addRole($role);
		}
		$manager->persist($groupAdmin);
		$manager->flush();
		$this->addReference('lycan-group', $groupAdmin);
	}
	
	private function loadUsers(ObjectManager $manager)
	{
		$user = new User();
		$user
			->setUsername('admin')
			->setEmail('admin@admin.com')
			->setPlainPassword('admin')
			->setRoles(['ROLE_SUPER_ADMIN'])
			->setEnabled(true);
		
		$manager->persist($user);
		
		$users = [
			"richard",
			"stewart",
			"george"
		];
		foreach($users as $key=>$u){
			$user = new User();
			$user->setUsername($u)
				->setEmail($u . '@example.com')
				->setPlainPassword('utopia')
				->setEnabled(true);
			$user->addGroup($this->getReference('lycan-group'));
			$manager->persist($user);
			$this->addReference('user-' . $u, $user);
		}

		$manager->flush();
	}
	
	
	
}
