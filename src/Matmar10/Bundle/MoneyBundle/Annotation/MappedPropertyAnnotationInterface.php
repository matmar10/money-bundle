<?php

namespace Matmar10\Bundle\MoneyBundle\Annotation;

/**
 * MappedPropertyAnnotationInterface
 *
 * @bundle matmar10-money-bundle
 */
interface MappedPropertyAnnotationInterface
{

    /**
     * Applies validation rules and prepares the map and options
     *
     * @abstract
     * @return self
     */
    public function init();

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

    /**
     * Returns an associative array of options configured by the annotation
     *
     * @abstract
     * @return array The associative array of options
     */
    public function getOptions();

}
