<?php

namespace Matmar10\Bundle\MoneyBundle\Annotation;

use Matmar10\Bundle\MoneyBundle\Annotation\CompositeProperty;
use ReflectionProperty;

abstract class BaseCompositeProperty implements CompositeProperty
{

    /**
     * @var boolean
     */
    public $nullable;

    /**
     * {inheritDoc}
     */
    abstract public function getMap(ReflectionProperty $reflectionProperty);

    /**
     * {inheritDoc}
     */
    abstract public function getClass();

    /**
     * {inheritDoc}
     */
    public function getNullable()
    {
        return $this->nullable;
    }

}
