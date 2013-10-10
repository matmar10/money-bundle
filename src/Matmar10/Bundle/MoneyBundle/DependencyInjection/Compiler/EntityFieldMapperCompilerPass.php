<?php

namespace Matmar10\Bundle\MoneyBundle\DependencyInjection\Compiler;

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class EntityFieldMapperCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {

        if (!$container->hasDefinition('matmar10_money.field_mapper')) {
            return;
        }

        $definition = $container->getDefinition('matmar10_money.field_mapper');
        $taggedServices = $container->findTaggedServiceIds('matmar10_money.field_mapper');

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
                    'registerMapper',
                    array(
                        $annotationClassName,
                        new Reference($dicServiceId),
                    )
                );
            }
        }
    }
}