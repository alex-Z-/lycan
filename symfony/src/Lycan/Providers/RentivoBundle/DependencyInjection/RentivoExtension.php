<?php

namespace Lycan\Providers\RentivoBundle\DependencyInjection;

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
class RentivoExtension extends Extension implements PrependExtensionInterface
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
		$loader->load('config.yml');
		
    }

	private function _addBundleParams( ContainerBuilder $container){
		
		
		if($container->hasParameter('lycan.core.providers')) {
			$providers = $container->getParameter('lycan.core.providers');
		} else {
			$providers = [];
		}
		$providers[] = [	'name' => 'Rentivo',
							'adminClass' => 'Lycan\Providers\RentivoBundle\Admin\ProviderRentivoAdmin',
							'entityClass' => 'Lycan\Providers\RentivoBundle\Entity\ProviderRentivoAuth'
						];
		$container->setParameter( 'lycan.core.providers',  $providers );
	}
    
	public function prepend(ContainerBuilder $container)
	{
		
		$container->loadFromExtension('core', array(
			'providers' => array(
				['name' => "RentivoBundle"]
			),
		));
		
		$container->loadFromExtension('guzzle', [
				'clients' => [
					'rentivo' => [
						'base_url' => "https://www.rentivo.com/"
					]
				]
			]);
		
	}

}
