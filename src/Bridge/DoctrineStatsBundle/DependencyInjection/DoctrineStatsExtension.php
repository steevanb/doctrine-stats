<?php

declare(strict_types=1);

namespace Steevanb\DoctrineStats\Bridge\DoctrineStatsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\{
    ContainerBuilder,
    Loader
};

class DoctrineStatsExtension extends Extension
{
    /** @param array<mixed> $configs */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $this
            ->loadServices($container)
            ->loadConfigs($configs, $container);
    }

    protected function loadServices(ContainerBuilder $container): self
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        return $this;
    }

    /** @param array<mixed> $configs */
    protected function loadConfigs(array $configs, ContainerBuilder $container): self
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $sqlLogger = $container->getDefinition('doctrine_stats.logger.sql');
        $sqlLogger->addMethodCall('setBacktraceEnabled', [$config['query_backtrace']]);

        return $this;
    }
}
