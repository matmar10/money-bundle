<?php

namespace Lmh\Bundle\MoneyBundle\Mapper;

use InvalidArgumentException;
use Lmh\Bundle\MoneyBundle\Annotation\MappedPropertyAnnotationInterface;
use Lmh\Bundle\MoneyBundle\Mapper\DefaultMapper;
use Lmh\Bundle\MoneyBundle\Mapper\EntityFieldMapperInterface;
use Lmh\Bundle\MoneyBundle\Service\CurrencyManager;
use ReflectionObject;
use ReflectionProperty;

class CurrencyMapper implements EntityFieldMapperInterface
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
         * @var $currencyInstance \Matmar10\Money\Entity\Currency
         */
        $currencyInstance = $reflectionProperty->getValue($entity);
        $currencyCode = $currencyInstance->getCurrencyCode();

        // lookup the currency code's field name based on the provided mapping
        $currencyCodePropertyName = $mappedProperties['currencyCode'];

        // set the currency code to the specified field name
        $currencyCodeReflectionProperty = new ReflectionProperty($entity, $currencyCodePropertyName);
        $currencyCodeReflectionProperty->setAccessible(true);
        $currencyCodeReflectionProperty->setValue($entity, $currencyCode);

        return $entity;
    }

    public function mapPostPersist(&$entity, ReflectionProperty $reflectionProperty, MappedPropertyAnnotationInterface $annotation)
    {
        $mappedProperties = $annotation->getMap();

        // lookup the currency code's field name based on the provided mapping
        $currencyCodePropertyName = $mappedProperties['currencyCode'];
        $currencyCodeReflectionProperty = new ReflectionProperty($entity, $currencyCodePropertyName);
        $currencyCodeReflectionProperty->setAccessible(true);
        $currencyCode = $currencyCodeReflectionProperty->getValue($entity);

        // build the currency instance from the currency manager using provided code
        $currencyInstance = $this->currencyManager->getCurrency($currencyCode);

        // set the currency instance on the original entities field
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($entity, $currencyInstance);

        return $entity;
    }
}
