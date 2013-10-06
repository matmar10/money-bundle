<?php

namespace Lmh\Bundle\MoneyBundle\Annotation;

use Lmh\Bundle\MoneyBundle\Annotation\BaseMappedPropertyAnnotation;
use Lmh\Bundle\MoneyBundle\Annotation\MappedPropertyAnnotationInterface;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class CurrencyPair extends BaseMappedPropertyAnnotation implements MappedPropertyAnnotationInterface
{
    /**
     * {inheritDoc}
     */
    public function getClass()
    {
        return '\\Matmar10\\Money\\Entity\\CurrencyPair';
    }

    /**
     * {inheritDoc}
     */
    public function getRequiredProperties()
    {
        return array(
            'fromCurrencyCode',
            'toCurrencyCode',
            'multiplier',
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
