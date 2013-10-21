<?php

namespace Matmar10\Bundle\MoneyBundle\PropertyStrategy;

use Matmar10\Bundle\MoneyBundle\Annotation\MappedPropertyAnnotationInterface;
use ReflectionProperty;

interface CompositePropertyStrategy
{
    public function flattenCompositeProperty(&$entity, ReflectionProperty $reflectionProperty, MappedPropertyAnnotationInterface $annotation);

    public function composeCompositeProperty(&$entity, ReflectionProperty $reflectionProperty, MappedPropertyAnnotationInterface $annotation);
}
