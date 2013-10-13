<?php

namespace Matmar10\Bundle\MoneyBundle\Mapper;

use InvalidArgumentException;
use Matmar10\Bundle\MoneyBundle\Annotation\MappedPropertyAnnotationInterface;
use Matmar10\Bundle\MoneyBundle\Exception\NullFieldMappingException;
use Matmar10\Bundle\MoneyBundle\Mapper\DefaultMapper;
use Matmar10\Bundle\MoneyBundle\Mapper\EntityFieldMapperInterface;
use Matmar10\Bundle\MoneyBundle\Service\CurrencyManager;
use Matmar10\Money\Entity\CurrencyPair;
use ReflectionObject;
use ReflectionProperty;

/**
 * {inheritDoc}
 */
class CurrencyPairMapper implements EntityFieldMapperInterface
{

    protected $currencyManager;

    public function __construct(CurrencyManager $currencyManager)
    {
        $this->currencyManager = $currencyManager;
    }

    public function mapPrePersist(&$entity, ReflectionProperty $reflectionProperty, MappedPropertyAnnotationInterface $annotation)
    {
        $annotation->init();
        $mappedProperties = $annotation->getMap();

        // get the currency code from the currency instance
        $reflectionProperty->setAccessible(true);

        /**
         * @var $currencyPairInstance \Matmar10\Money\Entity\CurrencyPair
         */
        $currencyPairInstance = $reflectionProperty->getValue($entity);

        // ignore if nullable and currency instance is null
        if(is_null($currencyPairInstance)) {
            $options = $annotation->getOptions();
            if($options['nullable']) {
                return $entity;
            }
            throw new NullFieldMappingException(sprintf("Mapped property '%s' cannot be null (to allow null value, use nullable=true)", $reflectionProperty->getName()));
        }

        $fromCurrency = $currencyPairInstance->getFromCurrency();
        if(is_null($fromCurrency)) {
            throw new NullFieldMappingException(sprintf('Cannot apply post persist property mapping for %s instance: required field %s is null', get_class($entity), 'fromCurrency'));
        }
        $toCurrency = $currencyPairInstance->getToCurrency();
        if(is_null($toCurrency)) {
            throw new NullFieldMappingException(sprintf('Cannot apply post persist property mapping for %s instance: required field %s is null', get_class($entity), 'toCurrency'));
        }

        // lookup the currency code's field name based on the provided mapping
        $fromCurrencyCodePropertyName = $mappedProperties['fromCurrencyCode'];
        $toCurrencyCodePropertyName = $mappedProperties['toCurrencyCode'];

        $fromCurrencyCodeReflectionProperty = new ReflectionProperty($entity, $fromCurrencyCodePropertyName);
        $fromCurrencyCodeReflectionProperty->setAccessible(true);
        $fromCurrencyCodeReflectionProperty->setValue($entity, $fromCurrency->getCurrencyCode());

        $toCurrencyCodeReflectionProperty = new ReflectionProperty($entity, $toCurrencyCodePropertyName);
        $toCurrencyCodeReflectionProperty->setAccessible(true);
        $toCurrencyCodeReflectionProperty->setValue($entity, $toCurrency->getCurrencyCode());

        return $entity;
    }

    public function mapPostPersist(&$entity, ReflectionProperty $reflectionProperty, MappedPropertyAnnotationInterface $annotation)
    {
        $annotation->init();
        $mappedProperties = $annotation->getMap();

        // lookup the currency code's field name based on the provided mapping
        $fromCurrencyCodePropertyName = $mappedProperties['fromCurrencyCode'];
        $toCurrencyCodePropertyName = $mappedProperties['toCurrencyCode'];

        $fromCurrencyCodeReflectionProperty = new ReflectionProperty($entity, $fromCurrencyCodePropertyName);
        $fromCurrencyCodeReflectionProperty->setAccessible(true);
        $fromCurrencyCode = $fromCurrencyCodeReflectionProperty->getValue($entity);
        if(is_null($fromCurrencyCode) || '' === $fromCurrencyCode) {
            throw new NullFieldMappingException(sprintf('Cannot apply post persist property mapping for %s instance: required field %s is null or blank', get_class($entity), $fromCurrencyCodePropertyName));
        }

        $toCurrencyCodeReflectionProperty = new ReflectionProperty($entity, $toCurrencyCodePropertyName);
        $toCurrencyCodeReflectionProperty->setAccessible(true);
        $toCurrencyCode = $toCurrencyCodeReflectionProperty->getValue($entity);
        if(is_null($fromCurrencyCode) || '' === $fromCurrencyCode) {
            throw new NullFieldMappingException(sprintf('Cannot apply post persist property mapping for %s instance: required field %s is null or blank', get_class($entity), $toCurrencyCodePropertyName));
        }

        // build the currency instance from the currency manager using provided code
        $fromCurrency = $this->currencyManager->getCurrency($fromCurrencyCode);
        $toCurrency = $this->currencyManager->getCurrency($toCurrencyCode);
        $currencyPairInstance = new CurrencyPair($fromCurrency, $toCurrency);

        // set the currency instance on the original entities field
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($entity, $currencyPairInstance);

        return $entity;
    }
}
