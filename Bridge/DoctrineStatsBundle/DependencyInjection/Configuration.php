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
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->booleanNode('query_backtrace')->defaultValue(false)->end()
            ->end();

        return $treeBuilder;
    }
}
