<?php

namespace Matmar10\Bundle\MoneyBundle\PropertyStrategy;

use Matmar10\Bundle\MoneyBundle\Annotation\CompositeProperty;
use Matmar10\Bundle\MoneyBundle\Exception\NullPropertyException;
use Matmar10\Bundle\MoneyBundle\Exception\UnexpectedClassException;
use Matmar10\Bundle\MoneyBundle\PropertyStrategy\CompositePropertyStrategy;
use ReflectionClass;
use ReflectionProperty;

abstract class BaseStrategy implements CompositePropertyStrategy
{
    protected function setProperty(&$entity, $propertyName, $propertyValue)
    {
        $reflectionClass = new ReflectionClass($entity);
        if($reflectionClass->hasProperty($propertyName)) {
            $property = new ReflectionProperty($entity, $propertyName);
            $property->setAccessible(true);
            $property->setValue($entity, $propertyValue);
            return;
        }
        $entity->$propertyName = $propertyValue;
    }

    protected function getProperty(&$entity, $propertyName)
    {
        $reflectionClass = new ReflectionClass($entity);
        if($reflectionClass->hasProperty($propertyName)) {
            $property = new ReflectionProperty($entity, $propertyName);
            $property->setAccessible(true);
            return $property->getValue($entity);
        }
        return $entity->$propertyName;
    }

    protected function getValues(&$entity, ReflectionProperty $reflectionProperty, CompositeProperty $annotation)
    {
        $map = $annotation->getMap($reflectionProperty);
        $values = array();
        foreach($map as $propertyName => $propertyMapping) {
            $value = $this->getProperty($entity, $propertyMapping['fieldName']);
            $values[$propertyName] = $value;
            if(is_null($value)) {
                if($propertyMapping['nullable']) {
                    continue;
                }
                $message = '%s entity has null value for required property %s';
                throw new NullPropertyException(sprintf($message, get_class($entity), $propertyMapping['fieldName']));
            }
            if('string' === $propertyMapping['type']) {
                if('' === $value) {
                    throw new NullPropertyException();
                }
            }
        }
        return $values;
    }

    protected function setValues(&$entity, ReflectionProperty $reflectionProperty, CompositeProperty $annotation, array $values)
    {
        $map = $annotation->getMap($reflectionProperty);
        foreach($map as $propertyName => $propertyMapping) {
            $value = $values[$propertyName];
            $this->setProperty($entity, $propertyMapping['fieldName'], $value);
        }
    }

    protected function getCompoundPropertyInstance(&$entity, ReflectionProperty $reflectionProperty, CompositeProperty $annotation)
    {
        $reflectionProperty->setAccessible(true);
        $instance = $reflectionProperty->getValue($entity);
        if(is_null($instance)) {
            if($annotation->getNullable()) {
                return false;
            }
            throw new NullPropertyException();
        }
        $this->assertInstanceClass($instance, $annotation);
        return $instance;
    }

    protected function assertInstanceClass($instance, CompositeProperty $annotation)
    {
        $reflectionClass = new ReflectionClass($annotation->getClass());
        if(!$reflectionClass->isInstance($instance)) {
            throw new UnexpectedClassException();
        }
    }
}
