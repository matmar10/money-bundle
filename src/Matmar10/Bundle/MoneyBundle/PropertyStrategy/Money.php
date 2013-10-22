<?php

namespace Matmar10\Bundle\MoneyBundle\PropertyStrategy;

use InvalidArgumentException;
use Matmar10\Bundle\MoneyBundle\Annotation\CompositeProperty;
use Matmar10\Bundle\MoneyBundle\Exception\NullPropertyException;
use Matmar10\Bundle\MoneyBundle\PropertyStrategy\CompositePropertyStrategy;
use Matmar10\Bundle\MoneyBundle\Service\CurrencyManager;
use ReflectionObject;
use ReflectionProperty;

/**
 * {inheritDoc}
 */
class Money implements CompositePropertyStrategy
{

    protected static $nullPropertyExceptionMessage = 'Cannot apply entity mapping for %s instance: required property %s is null or blank';

    protected $currencyManager;

    public function __construct(CurrencyManager $currencyManager)
    {
        $this->currencyManager = $currencyManager;
    }

    public function flattenCompositeProperty(&$entity, ReflectionProperty $reflectionProperty, CompositeProperty $annotation)
    {
        /**
         * @var $annotation \Matmar10\Bundle\MoneyBundle\Annotation\Money
         */

        // get the currency code from the currency instance
        $reflectionProperty->setAccessible(true);

        /**
         * @var $moneyInstance \Matmar10\Money\Entity\Money
         */
        $moneyInstance = $reflectionProperty->getValue($entity);

        // ignore if nullable and currency instance is null
        if(is_null($moneyInstance)) {
            if($annotation->getNullable()) {
                return;
            }
            throw new NullPropertyException();
        }

        /**
         * @var $currency \Matmar10\Money\Entity\Currency
         */
        $currency = $moneyInstance->getCurrency();
        if(is_null($currency)) {
            if($annotation->getNullable()) {
                return;
            }
            throw new NullPropertyException();
        }
        $currencyCode = $currency->getCurrencyCode();
        if(is_null($currencyCode)) {
            if($annotation->getNullable()) {
                return;
            }
            throw new NullPropertyException();
        }
        $amountInteger = $moneyInstance->getAmountInteger();
        if(is_null($amountInteger)) {
            if($annotation->getNullable()) {
                return;
            }
            throw new NullPropertyException();
        }

        $amountIntegerReflectionProperty = new ReflectionProperty($entity, $annotation->getAmountInteger());
        $amountIntegerReflectionProperty->setAccessible(true);
        $amountIntegerReflectionProperty->setValue($entity, (integer)$amountInteger);

        $currencyCodeReflectionProperty = new ReflectionProperty($entity, $annotation->getCurrencyCode());
        $currencyCodeReflectionProperty->setAccessible(true);
        $currencyCodeReflectionProperty->setValue($entity, $currencyCode);
    }

    public function composeCompositeProperty(&$entity, ReflectionProperty $reflectionProperty, CompositeProperty $annotation)
    {
        /**
         * @var $annotation \Matmar10\Bundle\MoneyBundle\Annotation\Money
         */

        $amountIntegerReflectionProperty = new ReflectionProperty($entity, $annotation->getAmountInteger());
        $amountIntegerReflectionProperty->setAccessible(true);
        $amountInteger = $amountIntegerReflectionProperty->getValue($entity);
        if(is_null($amountInteger) || '' === $amountInteger) {
            // ignore if nullable and currency instance is null
            if($annotation->getNullable()) {
                return $entity;
            }
            throw new NullPropertyException();
        }

        $currencyCodeReflectionProperty = new ReflectionProperty($entity, $annotation->getCurrencyCode());
        $currencyCodeReflectionProperty->setAccessible(true);
        $currencyCode = $currencyCodeReflectionProperty->getValue($entity);
        if(is_null($currencyCode) || '' === $currencyCode) {
            // ignore if nullable and currency instance is null
            if($annotation->getNullable()) {
                return $entity;
            }
            throw new NullPropertyException();
        }

        // build the currency instance from the currency manager using provided code
        $moneyInstance = $this->currencyManager->getMoney($currencyCode);
        $moneyInstance->setAmountInteger($amountInteger);

        // set the currency instance on the original entities field
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($entity, $moneyInstance);

        return $entity;
    }
}
