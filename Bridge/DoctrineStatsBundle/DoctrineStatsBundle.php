<?php

namespace steevanb\DoctrineStats\Bridge\DoctrineStatsBundle;

use steevanb\DoctrineStats\Bridge\DoctrineStatsBundle\DependencyInjection\Compiler\AddSqlLoggerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DoctrineStatsBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddSqlLoggerPass());
    }
}
