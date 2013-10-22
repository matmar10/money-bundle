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
class ExchangeRate implements CompositePropertyStrategy
{

    /**
     * @var \Matmar10\Bundle\MoneyBundle\Service\CurrencyManager
     */
    protected $currencyManager;

    public function __construct(CurrencyManager $currencyManager)
    {
        $this->currencyManager = $currencyManager;
    }

    public function flattenCompositeProperty(&$entity, ReflectionProperty $reflectionProperty, CompositeProperty $annotation)
    {
        /**
         * @var $annotation \Matmar10\Bundle\MoneyBundle\Annotation\ExchangeRate
         */

        // get the currency code from the currency instance
        $reflectionProperty->setAccessible(true);

        /**
         * @var $exchangeRateInstance \Matmar10\Money\Entity\ExchangeRate
         */
        $exchangeRateInstance = $reflectionProperty->getValue($entity);

        // ignore if nullable and currency instance is null
        if(is_null($exchangeRateInstance)) {
            if($annotation->getNullable()) {
                return;
            }
            throw new NullPropertyException();
        }

        $fromCurrency = $exchangeRateInstance->getFromCurrency();
        if(is_null($fromCurrency)) {
            throw new NullPropertyException();
        }
        $toCurrency = $exchangeRateInstance->getToCurrency();
        if(is_null($toCurrency)) {
            throw new NullPropertyException();
        }
        $multiplier = $exchangeRateInstance->getMultiplier();
        if(is_null($multiplier)) {
            throw new NullPropertyException();
        }

        $fromCurrencyCodeReflectionProperty = new ReflectionProperty($entity, $annotation->getFromCurrencyCode());
        $fromCurrencyCodeReflectionProperty->setAccessible(true);
        $fromCurrencyCodeReflectionProperty->setValue($entity, $fromCurrency->getCurrencyCode());

        $toCurrencyCodeReflectionProperty = new ReflectionProperty($entity, $annotation->getToCurrencyCode());
        $toCurrencyCodeReflectionProperty->setAccessible(true);
        $toCurrencyCodeReflectionProperty->setValue($entity, $toCurrency->getCurrencyCode());

        $multiplierCurrencyCodeReflectionProperty = new ReflectionProperty($entity, $annotation->getMultiplier());
        $multiplierCurrencyCodeReflectionProperty->setAccessible(true);
        $multiplierCurrencyCodeReflectionProperty->setValue($entity, $multiplier);
    }

    public function composeCompositeProperty(&$entity, ReflectionProperty $reflectionProperty, CompositeProperty $annotation)
    {
        /**
         * @var $annotation \Matmar10\Bundle\MoneyBundle\Annotation\ExchangeRate
         */

        $fromCurrencyCodeReflectionProperty = new ReflectionProperty($entity, $annotation->getFromCurrencyCode());
        $fromCurrencyCodeReflectionProperty->setAccessible(true);
        $fromCurrencyCode = $fromCurrencyCodeReflectionProperty->getValue($entity);
        if(is_null($fromCurrencyCode) || '' === $fromCurrencyCode) {
            // ignore if nullable and currency instance is null
            if($annotation->getNullable()) {
                return;
            }
            throw new NullPropertyException();
        }

        $toCurrencyCodeReflectionProperty = new ReflectionProperty($entity, $annotation->getToCurrencyCode());
        $toCurrencyCodeReflectionProperty->setAccessible(true);
        $toCurrencyCode = $toCurrencyCodeReflectionProperty->getValue($entity);
        if(is_null($toCurrencyCode) || '' === $toCurrencyCode) {
            // ignore if nullable and currency instance is null
            if($annotation->getNullable()) {
                return;
            }
            throw new NullPropertyException();
        }

        $multiplierCurrencyCodeReflectionProperty = new ReflectionProperty($entity, $annotation->getMultiplier());
        $multiplierCurrencyCodeReflectionProperty->setAccessible(true);
        $multiplier = $multiplierCurrencyCodeReflectionProperty->getValue($entity);
        if(is_null($multiplier)) {
            // ignore if nullable and currency instance is null
            if($annotation->getNullable()) {
                return;
            }
            throw new NullPropertyException();
        }

        // build the currency instance from the currency manager using provided code
        $fromCurrency = $this->currencyManager->getCurrency($fromCurrencyCode);
        $toCurrency = $this->currencyManager->getCurrency($toCurrencyCode);
        $className = $annotation->getClass();
        $exchangeRateInstance = new $className($fromCurrency, $toCurrency, $multiplier);

        // set the currency instance on the original entities field
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($entity, $exchangeRateInstance);

        return;
    }
}
