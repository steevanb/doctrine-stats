<?php

namespace steevanb\DoctrineStats\Bridge\DoctrineStatsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('doctrine_stats');
        $rootNode = \method_exists($treeBuilder, 'getRootNode') ? $treeBuilder->getRootNode() : $treeBuilder->root('doctrine_stats');

        $rootNode
            ->children()
                ->booleanNode('query_backtrace')->defaultValue(false)->end()
            ->end();

        return $treeBuilder;
    }
}
