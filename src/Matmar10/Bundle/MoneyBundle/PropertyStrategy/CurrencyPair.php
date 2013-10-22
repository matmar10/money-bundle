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
class CurrencyPair implements CompositePropertyStrategy
{

    protected $currencyManager;

    public function __construct(CurrencyManager $currencyManager)
    {
        $this->currencyManager = $currencyManager;
    }

    /**
     * {inheritDoc}
     */
    public function flattenCompositeProperty(&$entity, ReflectionProperty $reflectionProperty, CompositeProperty $annotation)
    {
        /**
         * @var $annotation \Matmar10\Bundle\MoneyBundle\Annotation\CurrencyPair
         */

        // get the currency code from the currency instance
        $reflectionProperty->setAccessible(true);

        /**
         * @var $currencyPairInstance \Matmar10\Money\Entity\CurrencyPair
         */
        $currencyPairInstance = $reflectionProperty->getValue($entity);

        // ignore if nullable and currency instance is null
        if(is_null($currencyPairInstance)) {
            if($annotation->getNullable()) {
                return;
            }
            throw new NullPropertyException();
        }

        $fromCurrency = $currencyPairInstance->getFromCurrency();
        if(is_null($fromCurrency)) {
            throw new NullPropertyException();
        }
        $toCurrency = $currencyPairInstance->getToCurrency();
        if(is_null($toCurrency)) {
            throw new NullPropertyException();
        }

        $fromCurrencyCodeReflectionProperty = new ReflectionProperty($entity, $annotation->getFromCurrencyCode());
        $fromCurrencyCodeReflectionProperty->setAccessible(true);
        $fromCurrencyCodeReflectionProperty->setValue($entity, $fromCurrency->getCurrencyCode());

        $toCurrencyCodeReflectionProperty = new ReflectionProperty($entity, $annotation->getToCurrencyCode());
        $toCurrencyCodeReflectionProperty->setAccessible(true);
        $toCurrencyCodeReflectionProperty->setValue($entity, $toCurrency->getCurrencyCode());
    }

    /**
     * {inheritDoc}
     */
    public function composeCompositeProperty(&$entity, ReflectionProperty $reflectionProperty, CompositeProperty $annotation)
    {
        /**
          * @var $annotation \Matmar10\Bundle\MoneyBundle\Annotation\CurrencyPair
          */

        $fromCurrencyCodeReflectionProperty = new ReflectionProperty($entity, $annotation->getFromCurrencyCode());
        $fromCurrencyCodeReflectionProperty->setAccessible(true);
        $fromCurrencyCode = $fromCurrencyCodeReflectionProperty->getValue($entity);
        if(is_null($fromCurrencyCode) || '' === $fromCurrencyCode) {
            throw new NullPropertyException();
        }

        $toCurrencyCodeReflectionProperty = new ReflectionProperty($entity, $annotation->getToCurrencyCode());
        $toCurrencyCodeReflectionProperty->setAccessible(true);
        $toCurrencyCode = $toCurrencyCodeReflectionProperty->getValue($entity);
        if(is_null($fromCurrencyCode) || '' === $fromCurrencyCode) {
            throw new NullPropertyException();
        }

        // build the currency instance from the currency manager using provided code
        $fromCurrency = $this->currencyManager->getCurrency($fromCurrencyCode);
        $toCurrency = $this->currencyManager->getCurrency($toCurrencyCode);
        $className = $annotation->getClass();
        $currencyPairInstance = new $className($fromCurrency, $toCurrency);

        // set the currency instance on the original entities field
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($entity, $currencyPairInstance);
    }
}
