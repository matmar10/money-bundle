<?php

namespace Matmar10\Bundle\MoneyBundle\PropertyStrategy;

use InvalidArgumentException;
use Matmar10\Bundle\MoneyBundle\Annotation\MappedPropertyAnnotationInterface;
use Matmar10\Bundle\MoneyBundle\Exception\NullFieldMappingException;
use Matmar10\Bundle\MoneyBundle\PropertyStrategy\CompositePropertyStrategy;
use Matmar10\Bundle\MoneyBundle\Service\CurrencyManager;
use Matmar10\Money\Entity\ExchangeRate as ExchangeRateEntity;
use Matmar10\Bundle\MoneyBundle\Exception\NullFieldException;
use ReflectionObject;
use ReflectionProperty;

/**
 * {inheritDoc}
 */
class ExchangeRate implements CompositePropertyStrategy
{

    protected static $nullPropertyExceptionMessage = 'Cannot apply entity mapping for %s instance: required property %s is null or blank';

    protected $currencyManager;

    public function __construct(CurrencyManager $currencyManager)
    {
        $this->currencyManager = $currencyManager;
    }

    public function flattenCompositeProperty(&$entity, ReflectionProperty $reflectionProperty, MappedPropertyAnnotationInterface $annotation)
    {
        $annotation->init();
        $mappedProperties = $annotation->getMap();
        $options = $annotation->getOptions();

        // get the currency code from the currency instance
        $reflectionProperty->setAccessible(true);

        /**
         * @var $exchangeRateInstance \Matmar10\Money\Entity\ExchangeRate
         */
        $exchangeRateInstance = $reflectionProperty->getValue($entity);

        // ignore if nullable and currency instance is null
        if(is_null($exchangeRateInstance)) {
            if($options['nullable']) {
                return $entity;
            }
            throw new NullFieldMappingException(sprintf("Mapped property '%s' cannot be null (to allow null value, use nullable=true)", $reflectionProperty->getName()));
        }

        $fromCurrency = $exchangeRateInstance->getFromCurrency();
        if(is_null($exchangeRateInstance)) {
            throw new InvalidArgumentException(sprintf(self::$nullPropertyExceptionMessage, get_class($entity, 'fromCurrency')));
        }
        $toCurrency = $exchangeRateInstance->getToCurrency();
        if(is_null($exchangeRateInstance)) {
            throw new InvalidArgumentException(sprintf(self::$nullPropertyExceptionMessage, get_class($entity, 'toCurrency')));
        }
        $multiplier = $exchangeRateInstance->getMultiplier();
        if(is_null($multiplier)) {
            throw new InvalidArgumentException(sprintf(self::$nullPropertyExceptionMessage, get_class($entity, 'multiplier')));
        }

        // lookup the currency code's field name based on the provided mapping
        $fromCurrencyCodePropertyName = $mappedProperties['fromCurrencyCode'];
        $toCurrencyCodePropertyName = $mappedProperties['toCurrencyCode'];
        $multiplierPropertyName = $mappedProperties['multiplier'];

        $fromCurrencyCodeReflectionProperty = new ReflectionProperty($entity, $fromCurrencyCodePropertyName);
        $fromCurrencyCodeReflectionProperty->setAccessible(true);
        $fromCurrencyCodeReflectionProperty->setValue($entity, $fromCurrency->getCurrencyCode());

        $toCurrencyCodeReflectionProperty = new ReflectionProperty($entity, $toCurrencyCodePropertyName);
        $toCurrencyCodeReflectionProperty->setAccessible(true);
        $toCurrencyCodeReflectionProperty->setValue($entity, $toCurrency->getCurrencyCode());

        $multiplierCurrencyCodeReflectionProperty = new ReflectionProperty($entity, $multiplierPropertyName);
        $multiplierCurrencyCodeReflectionProperty->setAccessible(true);
        $multiplierCurrencyCodeReflectionProperty->setValue($entity, $multiplier);

        return $entity;
    }

    public function composeCompositeProperty(&$entity, ReflectionProperty $reflectionProperty, MappedPropertyAnnotationInterface $annotation)
    {
        $annotation->init();
        $mappedProperties = $annotation->getMap();
        $options = $annotation->getOptions();

        // lookup the currency code's field name based on the provided mapping
        $fromCurrencyCodePropertyName = $mappedProperties['fromCurrencyCode'];
        $toCurrencyCodePropertyName = $mappedProperties['toCurrencyCode'];
        $multiplierPropertyName = $mappedProperties['multiplier'];

        $fromCurrencyCodeReflectionProperty = new ReflectionProperty($entity, $fromCurrencyCodePropertyName);
        $fromCurrencyCodeReflectionProperty->setAccessible(true);
        $fromCurrencyCode = $fromCurrencyCodeReflectionProperty->getValue($entity);
        if(is_null($fromCurrencyCode) || '' === $fromCurrencyCode) {
            // ignore if nullable and currency instance is null
            if($options['nullable']) {
                return $entity;
            }
            throw new InvalidArgumentException(sprintf(self::$nullPropertyExceptionMessage, get_class($entity), $fromCurrencyCodePropertyName));
        }

        $toCurrencyCodeReflectionProperty = new ReflectionProperty($entity, $toCurrencyCodePropertyName);
        $toCurrencyCodeReflectionProperty->setAccessible(true);
        $toCurrencyCode = $toCurrencyCodeReflectionProperty->getValue($entity);
        if(is_null($toCurrencyCode) || '' === $toCurrencyCode) {
            // ignore if nullable and currency instance is null
            if($options['nullable']) {
                return $entity;
            }
            throw new InvalidArgumentException(sprintf(self::$nullPropertyExceptionMessage, get_class($entity), $toCurrencyCodePropertyName));
        }

        $multiplierCurrencyCodeReflectionProperty = new ReflectionProperty($entity, $multiplierPropertyName);
        $multiplierCurrencyCodeReflectionProperty->setAccessible(true);
        $multiplier = $multiplierCurrencyCodeReflectionProperty->getValue($entity);
        if(is_null($multiplier)) {
            // ignore if nullable and currency instance is null
            if($options['nullable']) {
                return $entity;
            }
            throw new InvalidArgumentException(sprintf(self::$nullPropertyExceptionMessage, get_class($entity), $multiplierPropertyName));
        }

        // build the currency instance from the currency manager using provided code
        $fromCurrency = $this->currencyManager->getCurrency($fromCurrencyCode);
        $toCurrency = $this->currencyManager->getCurrency($toCurrencyCode);
        $exchangeRateInstance = new ExchangeRate($fromCurrency, $toCurrency, $multiplier);

        // set the currency instance on the original entities field
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($entity, $exchangeRateInstance);

        return $entity;
    }
}
