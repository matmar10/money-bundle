<?php

namespace Matmar10\Bundle\MoneyBundle\Annotation;

use Matmar10\Bundle\MoneyBundle\Annotation\CompositeProperty;

abstract class BaseCompositeProperty implements CompositeProperty
{

    public $nullable;

    /**
     * {inheritDoc}
     */
    abstract public function getMap();

    /**
     * {inheritDoc}
     */
    abstract public function getClass();

}
