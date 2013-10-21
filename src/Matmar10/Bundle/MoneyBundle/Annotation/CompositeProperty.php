<?php

namespace Matmar10\Bundle\MoneyBundle\Annotation;

/**
 * MappedPropertyAnnotationInterface
 *
 * @bundle matmar10-money-bundle
 */
interface CompositeProperty
{

    /**
     * Returns the expected fully qualified class path of the object field's instance
     *
     * @abstract
     * @return string The class associated with this property annotation
     */
    public function getClass();

    /**
     * Returns an associative array of properties to be mapped to
     *
     * @abstract
     * @return array The mapping of compound from entities properties to root-level entity properties
     */
    public function getMap();

}
