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
            ->fixXmlConfig('currency', 'currencies')
            ->children()
                ->scalarNode('currency_configuration_filename')
                    ->info('The base currency configuration filename')
                    ->isRequired()
                ->end()
                ->arrayNode('currencies')
                    ->info('Adds additional currencies.')
                    ->useAttributeAsKey('code')
                    ->fixXmlConfig('region')
                    ->prototype('array')
                        ->children()
                            ->integerNode('calculationPrecision')
                                ->isRequired()
                                ->min(0)
                                ->max(10)
                            ->end()
                            ->integerNode('displayPrecision')
                                ->isRequired()
                                ->min(0)
                                ->max(10)
                            ->end()
                            ->scalarNode('symbol')
                                ->defaultValue('')
                            ->end()
                            ->arrayNode('regions')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
        return $treeBuilder;
    }
}
