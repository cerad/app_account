<?php

namespace Cerad\Bundle\AccountBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

class DIExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // Simple merge for possible multiple source (dev etc)
        $config = array();
        foreach ($configs as $subConfig) 
        {
            $config = array_merge($config, $subConfig);
        }
        // For now just store as a parameter
        $container->setParameter('cerad_account_config',$config);
        
        // Continue
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
    public function getAlias() { return 'cerad_account'; }
}
