<?php

namespace Matmar10\Bundle\MoneyBundle\Annotation;

use Matmar10\Bundle\MoneyBundle\Annotation\MappedPropertyAnnotationInterface;
use Matmar10\Bundle\MoneyBundle\Exception\InvalidArgumentException;
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

    protected $map;

    public function __construct($options)
    {
        $this->options = $options;

        $required = $this->getRequiredProperties();
        $optional = $this->getOptionalProperties();
        $mapped = $this->getMappedProperties();

        $missingRequired = $required;

        foreach($this->options as $name => $value) {

            // required
            $position = array_search($name, $required);
            if(false !== $position) {
                // throw new NullFieldMappingException(sprintf("You must provide a valid mapping for required property '%s'", $name));
                if(!$value || '' === $value) {
                    throw new NullFieldMappingException(sprintf("You must provide a valid mapping for required property '%s'", $name));
                }
                unset($missingRequired[$position]);
                $this->map[$name] = $value;
                if(false !== array_search($name, $mapped)) {
                    $this->map[$name] = $value;
                }
                continue;
            }

            // optional
            if(false !== array_search($name, $optional)) {
                if(false !== array_search($name, $mapped)) {
                    $this->map[$name] = $value;
                }
                continue;
            }

            throw new InvalidArgumentException(sprintf("Unsupported option '%s' provided", $name));
        }

        if(count($missingRequired)) {
            throw new NullFieldMappingException(sprintf("You must provide a valid mapping for all required properties; missing properties are: %s", implode(',', $missingRequired)));
        }
    }

    public function getMap()
    {
        return $this->map;
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
     * @return array
     */
    public function getOptionalProperties()
    {
        return array(
            'nullable',
        );
    }

    /**
     * Returns an array of properties to be included in the map
     */
    abstract public function getMappedProperties();

    /**
     * {inheritDoc}
     */
    abstract public function getClass();

    /**
     * Gets the options as configured by the annotation
     *
     * @return array The options
     */
    public function getOptions()
    {
        return $this->options;
    }

}
