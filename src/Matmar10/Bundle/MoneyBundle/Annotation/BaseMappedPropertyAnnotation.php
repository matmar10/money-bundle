<?php

namespace Matmar10\Bundle\MoneyBundle\Annotation;

use Matmar10\Bundle\MoneyBundle\Annotation\MappedPropertyAnnotationInterface;
use Matmar10\Bundle\MoneyBundle\Exception\NullFieldMappingException;

/**
 * Convenience implementation for specifying property mappings by way of
 * implementing the getRequiredProperties and getOptionalProperties methods
 *
 * @bundle matmar10-money-bundle
 */
abstract class BaseMappedPropertyAnnotation implements MappedPropertyAnnotationInterface
{

    protected $options;

    public function __construct($options)
    {
        $this->options = $options;

        // assert required property names
        foreach($this->getRequiredProperties() as $requiredProperty) {
            if(false === array_key_exists($requiredProperty, $options)) {
                throw new NullFieldMappingException(sprintf("You must provide a valid mapping for required property '%s'", $requiredProperty));
            }
            if(!$this->options[$requiredProperty] || '' === $this->options[$requiredProperty]) {
                throw new NullFieldMappingException(sprintf("You must provide a valid mapping for required property '%s'", $requiredProperty));
            }
        }
    }

    public function getMap()
    {
        $map = array();
        foreach($this->getRequiredProperties() as $propertyName) {
            $map[$propertyName] = $this->options[$propertyName];
        }
        foreach($this->getOptionalProperties() as $propertyName) {
            if(!$this->options[$propertyName]) {
                continue;
            }
            $map[$propertyName] = $this->options[$propertyName];
        }
        return $map;
    }

    /**
     * Returns an array of properties which must be mapped in the annotation
     *
     * @abstract
     * @return array
     */
    abstract public function getRequiredProperties();

    /**
     * Returns an array of properties which may be mapped in the annotation but are not required
     *
     * @abstract
     * @return array
     */
    abstract public function getOptionalProperties();

    /**
     * {inheritDoc}
     */
    abstract public function getClass();

}
