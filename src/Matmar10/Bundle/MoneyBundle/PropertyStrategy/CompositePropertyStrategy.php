<?php

namespace Matmar10\Bundle\MoneyBundle\PropertyStrategy;

use Matmar10\Bundle\MoneyBundle\Annotation\CompositeProperty;
use ReflectionProperty;

interface CompositePropertyStrategy
{
    /**
     * Populates scalar values from a composite property using the annotated mapping
     *
     * @abstract
     * @param object $entity The entity to operate on
     * @param \ReflectionProperty $reflectionProperty The property to operate on
     * @param \Matmar10\Bundle\MoneyBundle\Annotation\CompositeProperty $annotation The composite property annotation
     * @return null
     */
    public function flattenCompositeProperty(&$entity, ReflectionProperty $reflectionProperty, CompositeProperty $annotation);

    /**
     * Composes a composite property from scalar values from using the annotated mapping
     *
     * @abstract
     * @param object $entity The entity to operate on
     * @param \ReflectionProperty $reflectionProperty The property to operate on
     * @param \Matmar10\Bundle\MoneyBundle\Annotation\CompositeProperty $annotation The composite property annotation
     * @return null
     */
    public function composeCompositeProperty(&$entity, ReflectionProperty $reflectionProperty, CompositeProperty $annotation);
}
