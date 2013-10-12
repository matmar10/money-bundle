<?php

namespace Matmar10\Bundle\MoneyBundle\Annotation;

use Matmar10\Bundle\MoneyBundle\Annotation\BaseMappedPropertyAnnotation;
use Matmar10\Bundle\MoneyBundle\Annotation\MappedPropertyAnnotationInterface;

/**
 * Currency annotation
 *
 * @bundle matmar10-money-bundle
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Currency extends BaseMappedPropertyAnnotation implements MappedPropertyAnnotationInterface
{
    /**
     * {inheritDoc}
     */
    public function getClass()
    {
        return '\\Matmar10\\Money\\Entity\\Currency';
    }

    /**
     * {inheritDoc}
     */
    public function getRequiredProperties()
    {
        return array(
            'currencyCode',
        );
    }

    /**
     * {inheritDoc}
     */
    public function getMappedProperties()
    {
        return array(
            'currencyCode',
        );
    }
}
