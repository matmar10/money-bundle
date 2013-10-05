<?php

namespace Lmh\Bundle\MoneyBundle\Mapper;

use InvalidArgumentException;
use Lmh\Bundle\MoneyBundle\Annotation\MappedPropertyAnnotationInterface;
use Lmh\Bundle\MoneyBundle\Mapper\DefaultMapper;
use Lmh\Bundle\MoneyBundle\Mapper\EntityFieldMapperInterface;
use Lmh\Bundle\MoneyBundle\Service\CurrencyManager;
use ReflectionObject;
use ReflectionProperty;

class MoneyMapper implements EntityFieldMapperInterface
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
         * @var $moneyInstance \Matmar10\Money\Entity\Money
         */
        $moneyInstance = $reflectionProperty->getValue($entity);

        /**
         * @var $currency \Matmar10\Money\Entity\Currency
         */
        $currency = $moneyInstance->getCurrency();

        $amountInteger = $moneyInstance->getAmountInteger();
        $currencyCode = $currency->getCurrencyCode();

        // lookup the currency code and amountInteger field names based on the provided mapping
        $amountIntegerPropertyName = $mappedProperties['amountInteger'];
        $currencyCodePropertyName = $mappedProperties['currencyCode'];

        $amountIntegerReflectionProperty = new ReflectionProperty($entity, $amountIntegerPropertyName);
        $amountIntegerReflectionProperty->setAccessible(true);
        $amountIntegerReflectionProperty->setValue($entity, $amountInteger);

        $currencyCodeReflectionProperty = new ReflectionProperty($entity, $currencyCodePropertyName);
        $currencyCodeReflectionProperty->setAccessible(true);
        $currencyCodeReflectionProperty->setValue($entity, $currencyCode);

        return $entity;
    }

    public function mapPostPersist(&$entity, ReflectionProperty $reflectionProperty, MappedPropertyAnnotationInterface $annotation)
    {
        $mappedProperties = $annotation->getMap();

        // lookup the currency code and amountInteger field names based on the provided mapping
        $amountIntegerPropertyName = $mappedProperties['amountInteger'];
        $currencyCodePropertyName = $mappedProperties['currencyCode'];

        $amountIntegerReflectionProperty = new ReflectionProperty($entity, $amountIntegerPropertyName);
        $amountIntegerReflectionProperty->setAccessible(true);
        $amountInteger = $amountIntegerReflectionProperty->getValue($entity);

        $currencyCodeReflectionProperty = new ReflectionProperty($entity, $currencyCodePropertyName);
        $currencyCodeReflectionProperty->setAccessible(true);
        $currencyCode = $currencyCodeReflectionProperty->getValue($entity);

        // build the currency instance from the currency manager using provided code
        $moneyInstance = $this->currencyManager->getMoney($currencyCode);
        $moneyInstance->setAmountInteger($amountInteger);

        // set the currency instance on the original entities field
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($entity, $moneyInstance);

        return $entity;
    }
}
