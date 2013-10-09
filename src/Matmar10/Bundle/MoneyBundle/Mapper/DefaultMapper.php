<?php

namespace Matmar10\Bundle\MoneyBundle\Mapper;

use InvalidArgumentException;
use Matmar10\Bundle\MoneyBundle\Annotation\MappedPropertyAnnotationInterface;
use Matmar10\Bundle\MoneyBundle\Mapper\EntityFieldMapperInterface;
use ReflectionObject;
use ReflectionProperty;

/**
 * {inheritDoc}
 */
class DefaultMapper implements EntityFieldMapperInterface
{
    public function mapPrePersist(&$entity, ReflectionProperty $reflectionProperty, MappedPropertyAnnotationInterface $annotation)
    {
        $reflectionProperty->setAccessible(true);
        $fromPropertyValue = $reflectionProperty->getValue();
        $mappedProperties = $annotation->getMap();
        foreach($mappedProperties as $fromSubPropertyName => $toPropertyName) {
            // this reads a sub-property and assigns it to an entity-level property
            // examples: $entity->amountInteger = $entity->amount->amountInteger
            // or: $entity->currencyCode = $entity->currency->currencyCode
            $fromSubPropertyReflection = new ReflectionProperty($fromPropertyValue, $fromSubPropertyName);
            $fromSubPropertyReflection->setAccessible(true);
            $fromSubPropertyValue = $fromSubPropertyReflection->getValue();
            $toRootPropertyRefection = new ReflectionProperty($entity, $toPropertyName);
            $toRootPropertyRefection->setAccessible(true);
            $toRootPropertyRefection->setValue($fromSubPropertyValue);
        }
    }

    public function mapPostPersist(&$entity, ReflectionProperty $reflectionProperty, MappedPropertyAnnotationInterface $annotation)
    {
        $reflectionProperty->setAccessible(true);
        $mappedProperties = $annotation->getMap();

        $instanceClass = $annotation->getClass();
        $propertyInstance = new $instanceClass;

        foreach($mappedProperties as $toSubPropertyName => $fromPropertyName) {

            // this reads a sub-property and assigns it to an entity-level property
            // examples: $entity->amount->amountInteger = $entity->amountInteger
            // or: $entity->currency->currencyCode = $entity->currencyCode

            // get access to the new instance's inner property
            $toSubPropertyReflection = new ReflectionProperty($propertyInstance, $toSubPropertyName);
            $toSubPropertyReflection->setAccessible(true);

            // get the value from the entity's root-level property
            $fromRootPropertyRefection = new ReflectionProperty($entity, $fromPropertyName);
            $fromRootPropertyRefection->setAccessible(true);
            $toSubPropertyValue = $fromRootPropertyRefection->getValue($entity);

            // set the instance's sub-property value from the entity's root-level property
            $toSubPropertyReflection->setValue($propertyInstance, $toSubPropertyValue);
        }

        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($entity, $propertyInstance);

        return $entity;
    }
}
