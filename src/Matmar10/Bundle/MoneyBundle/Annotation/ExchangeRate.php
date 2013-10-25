<?php

namespace Matmar10\Bundle\MoneyBundle\Annotation;

use Matmar10\Bundle\MoneyBundle\Annotation\BaseCompositeProperty;
use Matmar10\Bundle\MoneyBundle\Annotation\CompositeProperty;
use ReflectionProperty;

/**
 * ExchangeRate annotation
 *
 * @bundle matmar10-money-bundle
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
class ExchangeRate extends BaseCompositeProperty implements CompositeProperty
{
    /**
     * @var string
     */
    public $fromCurrencyCode = null;

    /**
     * @var string
     */
    public $toCurrencyCode = null;

    /**
     * @var string
     */
    public $multiplier = null;

    /**
     * {inheritDoc}
     */
    public function getClass()
    {
        return '\\Matmar10\\Money\\Entity\\ExchangeRate';
    }

    /**
     * {inheritDoc}
     */
    public function getMap(ReflectionProperty $reflectionProperty)
    {
        $fromCurrencyCodePropertyName = (is_null($this->fromCurrencyCode)) ?
            $reflectionProperty->getName() . 'FromCurrencyCode' :
            $this->fromCurrencyCode;
        $toCurrencyCodePropertyName = (is_null($this->toCurrencyCode)) ?
            $reflectionProperty->getName() . 'ToCurrencyCode' :
            $this->toCurrencyCode;
        $multiplierPropertyName = (is_null($this->multiplier)) ?
            $reflectionProperty->getName() . 'Multiplier' :
            $this->multiplier;

        return array(
            'fromCurrencyCode' => array(
                'length' => 3,
                'fieldName' => $fromCurrencyCodePropertyName,
                'nullable' => $this->nullable,
                'type' => 'string',
            ),
            'toCurrencyCode' => array(
                'length' => 3,
                'fieldName' => $toCurrencyCodePropertyName,
                'nullable' => $this->nullable,
                'type' => 'string',
            ),
            'multiplier' => array(
                'fieldName' => $multiplierPropertyName,
                'nullable' => $this->nullable,
                'type' => 'float',
            ),
        );
    }

    /**
     * @return string
     */
    public function getFromCurrencyCode()
    {
        return $this->fromCurrencyCode;
    }

    /**
     * @return string
     */
    public function getMultiplier()
    {
        return $this->multiplier;
    }

    /**
     * @return string
     */
    public function getToCurrencyCode()
    {
        return $this->toCurrencyCode;
    }
}
