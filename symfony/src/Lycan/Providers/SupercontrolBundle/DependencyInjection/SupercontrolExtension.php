<?php

namespace Lycan\Providers\SupercontrolBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SupercontrolExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
	
		$this->_addBundleParams($container);
		
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
	
	private function _addBundleParams( ContainerBuilder $container){
		
		if($container->hasParameter('lycan.core.providers')) {
			$providers = $container->getParameter('lycan.core.providers');
		} else {
			$providers = [];
		}
		$providers[] = [	'name' => 'Supercontrol',
							'adminClass' => 'Lycan\Providers\SupercontrolBundle\Admin\ProviderSupercontrolAdmin',
							'entityClass' => 'Lycan\Providers\SupercontrolBundle\Entity\ProviderSupercontrolAuth'
		];
		$container->setParameter( 'lycan.core.providers',  $providers );
		
	}
	
    public function prepend(ContainerBuilder $container)
	{
		/// THIS DOES NOTHING??
		$container->loadFromExtension('core', array(
			'providers' => array(
				['name' => "Supercontrol"]
			),
		));
		
	}
    
}
