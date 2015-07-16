<?php

namespace Matmar10\Bundle\MoneyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('lmh_money');

/*
    matmar10_money.composite_property_subscriber:
        class: "%matmar10_money.composite_property_subscriber.class%"
        arguments: [ @matmar10_money.composite_property_service ]
        # tags:
            # - { name: doctrine.event_subscriber }
            # - { name: doctrine.event_subscriber, connection: default }
 */


        $rootNode
            ->fixXmlConfig('currency', 'currencies')
            ->children()
                ->arrayNode('doctrine_connections')
                    ->info('Connections that the composite property subscriber should listen for')
                    ->prototype('scalar')->end()
                ->end()
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
                            ->scalarNode('alias')
                        ->end()
                    ->end()
                    ->beforeNormalization()
                        ->ifTrue(function($v) {
                            return isset($v['alias']);
                        })
                        ->then(function($v) {
                            //set a value, because this fields are require
                            $v['calculationPrecision'] = $v['displayPrecision'] = 0;
                            return $v;
                        })
                    ->end()
                ->end()
            ->end();
        return $treeBuilder;
    }
}
