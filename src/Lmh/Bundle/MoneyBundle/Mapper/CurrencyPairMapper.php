<?php

namespace Lmh\Bundle\MoneyBundle\Mapper;

use InvalidArgumentException;
use Lmh\Bundle\MoneyBundle\Annotation\MappedPropertyAnnotationInterface;
use Lmh\Bundle\MoneyBundle\Mapper\DefaultMapper;
use Lmh\Bundle\MoneyBundle\Mapper\EntityFieldMapperInterface;
use Lmh\Bundle\MoneyBundle\Service\CurrencyManager;
use Matmar10\Money\Entity\CurrencyPair;
use ReflectionObject;
use ReflectionProperty;

class CurrencyPairMapper implements EntityFieldMapperInterface
{

    protected $currencyManager;

    public function __construct(CurrencyManager $currencyManager)
    {
        $this->currencyManager = $currencyManager;
    }

    public function mapPrePersist(&$entity, ReflectionProperty $reflectionProperty, MappedPropertyAnnotationInterface $annotation)
    {
        $mappedProperties = $annotation->getMap();

        // get the currency code from the currency instance
        $reflectionProperty->setAccessible(true);

        /**
         * @var $currencyPairInstance \Matmar10\Money\Entity\CurrencyPair
         */
        $currencyPairInstance = $reflectionProperty->getValue($entity);
        $fromCurrency = $currencyPairInstance->getFromCurrency();
        $toCurrency = $currencyPairInstance->getToCurrency();
        $multiplier = $currencyPairInstance->getMultiplier();

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

    public function mapPostPersist(&$entity, ReflectionProperty $reflectionProperty, MappedPropertyAnnotationInterface $annotation)
    {
        $mappedProperties = $annotation->getMap();

        // lookup the currency code's field name based on the provided mapping
        $fromCurrencyCodePropertyName = $mappedProperties['fromCurrencyCode'];
        $toCurrencyCodePropertyName = $mappedProperties['toCurrencyCode'];
        $multiplierPropertyName = $mappedProperties['multiplier'];

        $fromCurrencyCodeReflectionProperty = new ReflectionProperty($entity, $fromCurrencyCodePropertyName);
        $fromCurrencyCodeReflectionProperty->setAccessible(true);
        $fromCurrencyCode = $fromCurrencyCodeReflectionProperty->getValue($entity);

        $toCurrencyCodeReflectionProperty = new ReflectionProperty($entity, $toCurrencyCodePropertyName);
        $toCurrencyCodeReflectionProperty->setAccessible(true);
        $toCurrencyCode = $fromCurrencyCodeReflectionProperty->getValue($entity);

        $multiplierCurrencyCodeReflectionProperty = new ReflectionProperty($entity, $multiplierPropertyName);
        $multiplierCurrencyCodeReflectionProperty->setAccessible(true);
        $multiplier = $multiplierCurrencyCodeReflectionProperty->getValue($entity);

        // build the currency instance from the currency manager using provided code
        $fromCurrency = $this->currencyManager->getCurrency($fromCurrencyCode);
        $toCurrency = $this->currencyManager->getCurrency($toCurrencyCode);
        $currencyPairInstance = new CurrencyPair($fromCurrency, $toCurrency, $multiplier);

        // set the currency instance on the original entities field
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($entity, $currencyPairInstance);

        return $entity;
    }
}
