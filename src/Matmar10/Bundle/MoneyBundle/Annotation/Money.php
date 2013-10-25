<?php

namespace Matmar10\Bundle\MoneyBundle\Annotation;

use Matmar10\Bundle\MoneyBundle\Annotation\BaseCompositeProperty;
use Matmar10\Bundle\MoneyBundle\Annotation\CompositeProperty;
use Matmar10\Bundle\MoneyBundle\Exception\InvalidArgumentException;
use ReflectionProperty;

/**
 * Money
 *
 * @bundle matmar10-money-bundle
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Money extends BaseCompositeProperty implements CompositeProperty
{

    /**
     * @var string
     */
    public $currencyCode = null;

    /**
     * @var string
     */
    public $amountInteger = null;

    /**
     * {inheritDoc}
     */
    public function getClass()
    {
        return '\\Matmar10\\Money\\Entity\\Money';
    }

    /**
     * {inheritDoc}
     */
    public function getMap(ReflectionProperty $reflectionProperty)
    {
        $currencyCodePropertyName = (is_null($this->currencyCode)) ?
            $reflectionProperty->getName() . 'CurrencyCode' :
            $this->currencyCode;
        $amountIntegerPropertyName = (is_null($this->amountInteger)) ?
            $reflectionProperty->getName() . 'AmountInteger' :
            $this->amountInteger;
        return array(
            'currencyCode' => array(
                 'length' => 3,
                 'fieldName' => $currencyCodePropertyName,
                 'nullable' => $this->nullable,
                 'type' => 'string',
             ),
            'amountInteger' => array(
                'fieldName' => $amountIntegerPropertyName,
                'nullable' => $this->nullable,
                'type' => 'bigint',
            ),
        );
    }

    /**
     * @return int
     */
    public function getAmountInteger()
    {
        return $this->amountInteger;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }
}
