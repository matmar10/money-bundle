<?php

namespace Lmh\Bundle\MoneyBundle\Annotation;

use InvalidArgumentException;
use Lmh\Bundle\MoneyBundle\Annotation\BaseMappedPropertyAnnotation;
use Lmh\Bundle\MoneyBundle\Annotation\MappedPropertyAnnotationInterface;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Money extends BaseMappedPropertyAnnotation implements MappedPropertyAnnotationInterface
{
    /**
     * {inheritDoc}
     */
    public function getClass()
    {
        return '\\Matmar10\\Money\\Entity\\Money';
    }

    /**
     * {inheritDoc}
     */
    public function getRequiredProperties()
    {
        return array(
            'currencyCode',
            'amountInteger',
        );
    }

    /**
     * {inheritDoc}
     */
    public function getOptionalProperties()
    {
        return array();
    }
}
