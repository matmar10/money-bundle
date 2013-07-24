<?php

namespace Lmh\Bundle\MoneyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('lmh_money');

        $rootNode
            ->children()
                ->scalarNode('currency_configuration_filename')
            ->end();

/*
        $rootNode
            ->children()

                ->arrayNode('currencies')
                    ->info('Adds additional currencies.')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->integerNode('calculation')
                                ->isRequired()
                                ->min(0)
                                ->max(10)
                            ->end()
                            ->integerNode('display')
                                ->isRequired()
                                ->min(0)
                                ->max(10)
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('regions')
                    ->info('Maps region codes to currency codes')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('scalar')->end()
                ->end()

                ->arrayNode('symbols')
                    ->info('Currency symbols for various currencies')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('scalar')->end()
                ->end()

            ->end();
*/
        return $treeBuilder;
    }
}
