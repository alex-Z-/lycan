<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
	
	public function __construct($environment, $debug)
	{
		parent::__construct($environment, $debug);
		
		$config = new Doctrine\ORM\Configuration;
		// Your configs..
		$config->addFilter('soft-deleteable', 'Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter');
		
		
		
	}
	
	public function registerBundles()
    {
	
		
		
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new AppBundle\AppBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
	        new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),

	    	//Sonata Bundle
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),

            //Storage & sonata admin
            new \Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new \Sonata\AdminBundle\SonataAdminBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Sonata\UserBundle\SonataUserBundle('FOSUserBundle'),

            new Application\Sonata\UserBundle\ApplicationSonataUserBundle(),
           
            new Lycan\Providers\RentivoBundle\RentivoBundle(),
			new Lycan\Providers\CoreBundle\CoreBundle(),
			new Raindrop\SonataThemeBundle\RaindropSonataThemeBundle('SonataAdminBundle'),
			// KEEP THIS REMOVED??
			new Lycan\AclSonataAdminBundle\AclSonataAdminBundle(),
			new Oneup\AclBundle\OneupAclBundle()
        );
		
		
		if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
		
			
			$bundles[] = new CoreSphere\ConsoleBundle\CoreSphereConsoleBundle();
		}

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
