<?php

namespace Matmar10\Bundle\MoneyBundle\DependencyInjection;

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class CompositePropertyStrategiesCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {

        if (!$container->hasDefinition('matmar10_money.composite_property_service')) {
            return;
        }

        $definition = $container->getDefinition('matmar10_money.composite_property_service');
        $taggedServices = $container->findTaggedServiceIds('composite_property_strategy');

        foreach($taggedServices as $dicServiceId => $taggedAnnotations) {
            foreach($taggedAnnotations as $attributes) {
                if(!isset($attributes['annotation'])) {
                    throw new InvalidArgumentException("Invalid lmh_money.field_mapper tag: no annotation attribute provided.");
                }

                $annotationClassName = $attributes['annotation'];
                if(0 === stripos($annotationClassName, '\\')) {
                    $annotationClassName = substr($annotationClassName, 1);
                }

                $definition->addMethodCall(
                    'registerStrategy',
                    array(
                        $annotationClassName,
                        new Reference($dicServiceId),
                    )
                );
            }
        }
    }
}