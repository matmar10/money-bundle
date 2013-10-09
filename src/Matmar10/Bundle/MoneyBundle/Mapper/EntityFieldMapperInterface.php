<?php

namespace Matmar10\Bundle\MoneyBundle\Mapper;

use Matmar10\Bundle\MoneyBundle\Annotation\MappedPropertyAnnotationInterface;
use ReflectionProperty;

interface EntityFieldMapperInterface
{
    public function mapPrePersist(&$entity, ReflectionProperty $reflectionProperty, MappedPropertyAnnotationInterface $annotation);

    public function mapPostPersist(&$entity, ReflectionProperty $reflectionProperty, MappedPropertyAnnotationInterface $annotation);
}
