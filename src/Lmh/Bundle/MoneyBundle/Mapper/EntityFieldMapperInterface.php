<?php

namespace Lmh\Bundle\MoneyBundle\Mapper;

use Lmh\Bundle\MoneyBundle\Annotation\MappedPropertyAnnotationInterface;
use ReflectionProperty;

interface EntityFieldMapperInterface
{
    public function mapPrePersist(&$entity, ReflectionProperty $reflectionProperty, MappedPropertyAnnotationInterface $annotation);

    public function mapPostPersist(&$entity, ReflectionProperty $reflectionProperty, MappedPropertyAnnotationInterface $annotation);
}
