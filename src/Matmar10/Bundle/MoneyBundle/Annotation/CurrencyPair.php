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
class CurrencyPair extends BaseCompositeProperty implements CompositeProperty
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
     * {inheritDoc}
     */
    public function getClass()
    {
        return '\\Matmar10\\Money\\Entity\\CurrencyPair';
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
    public function getToCurrencyCode()
    {
        return $this->toCurrencyCode;
    }
}
