<?php

declare(strict_types=1);

namespace Steevanb\DoctrineStats\Bridge\DoctrineStatsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\{
    Compiler\CompilerPassInterface,
    ContainerBuilder,
    Reference
};

class AddSqlLoggerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('doctrine.dbal.logger.chain');
        $definition->addMethodCall('addLogger', [new Reference('doctrine_stats.logger.sql')]);
    }
}
