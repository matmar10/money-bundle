<?php

namespace Matmar10\Bundle\MoneyBundle\Annotation;

use Matmar10\Bundle\MoneyBundle\Annotation\BaseMappedPropertyAnnotation;
use Matmar10\Bundle\MoneyBundle\Annotation\MappedPropertyAnnotationInterface;

/**
 * ExchangeRate annotation
 *
 * @bundle matmar10-money-bundle
 *
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
