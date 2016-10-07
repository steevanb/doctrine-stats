<?php

namespace steevanb\DoctrineStats\Bridge\DoctrineStatsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
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
            ->loadConfigs($configs, $container);
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
     * @param array $configs
     * @param ContainerBuilder $container
     * @return $this
     */
    protected function loadConfigs(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $sqlLogger = $container->getDefinition('doctrine_stats.logger.sql');
        $sqlLogger->addMethodCall('setBacktraceEnabled', [$config['query_backtrace']]);

        return $this;
    }
}
