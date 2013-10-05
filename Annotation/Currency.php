<?php

namespace Lmh\Bundle\MoneyBundle\Annotation;

use Lmh\Bundle\MoneyBundle\Annotation\BaseMappedPropertyAnnotation;
use Lmh\Bundle\MoneyBundle\Annotation\MappedPropertyAnnotationInterface;

/**
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
            'currencyCode'
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
