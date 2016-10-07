<?php

namespace steevanb\DoctrineStats\Bridge\DoctrineStatsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class DoctrineStatsExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this
            ->loadServices($container)
            ->addSqlLogger($container);
    }

    /**
     * @param ContainerBuilder $container
     * @return $this
     */
    protected function loadServices(ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        return $this;
    }

    /**
     * @param ContainerBuilder $container
     * @return $this
     */
    protected function addSqlLogger(ContainerBuilder $container)
    {
//        d('test 1');
//        $container
//            ->getDefinition('data_collector.doctrine')
//            ->addMethodCall('addLogger', ['default', new Reference('doctrine_stats.logging.sql')]);

        return $this;
    }
}
