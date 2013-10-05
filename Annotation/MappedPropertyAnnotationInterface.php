<?php

namespace Lmh\Bundle\MoneyBundle\Annotation;

interface MappedPropertyAnnotationInterface
{

    /**
     * Returns the expected fully qualified class path of the object field's instance
     *
     * @abstract
     * @return string
     */
    public function getClass();

    /**
     * Returns an associative array of properties to be mapped to
     *
     * @abstract
     * @return array
     */
    public function getMap();

}
