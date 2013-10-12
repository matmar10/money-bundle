<?php

namespace Matmar10\Bundle\MoneyBundle\Mapper;

use InvalidArgumentException;
use Matmar10\Bundle\MoneyBundle\Annotation\MappedPropertyAnnotationInterface;
use Matmar10\Bundle\MoneyBundle\Mapper\DefaultMapper;
use Matmar10\Bundle\MoneyBundle\Mapper\EntityFieldMapperInterface;
use Matmar10\Bundle\MoneyBundle\Service\CurrencyManager;
use ReflectionObject;
use ReflectionProperty;

/**
 * {inheritDoc}
 */
class MoneyMapper implements EntityFieldMapperInterface
{

    protected static $nullPropertyExceptionMessage = 'Cannot apply entity mapping for %s instance: required property %s is null or blank';

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

        // ignore if nullable and currency instance is null
        if(is_null($moneyInstance)) {
            $options = $annotation->getOptions();
            if($options['nullable']) {
                return $entity;
            }
        }

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
        $amountIntegerReflectionProperty->setValue($entity, (integer)$amountInteger);

        $currencyCodeReflectionProperty = new ReflectionProperty($entity, $currencyCodePropertyName);
        $currencyCodeReflectionProperty->setAccessible(true);
        $currencyCodeReflectionProperty->setValue($entity, $currencyCode);

        return $entity;
    }

    public function mapPostPersist(&$entity, ReflectionProperty $reflectionProperty, MappedPropertyAnnotationInterface $annotation)
    {
        $mappedProperties = $annotation->getMap();
        $options = $annotation->getOptions();

        // lookup the currency code and amountInteger field names based on the provided mapping
        $amountIntegerPropertyName = $mappedProperties['amountInteger'];
        $currencyCodePropertyName = $mappedProperties['currencyCode'];

        $amountIntegerReflectionProperty = new ReflectionProperty($entity, $amountIntegerPropertyName);
        $amountIntegerReflectionProperty->setAccessible(true);
        $amountInteger = $amountIntegerReflectionProperty->getValue($entity);
        if(is_null($amountInteger) || '' === $amountInteger) {
            // ignore if nullable and currency instance is null
            if($options['nullable']) {
                return $entity;
            }
            throw new InvalidArgumentException(sprintf(self::$nullPropertyExceptionMessage, get_class($entity), $amountIntegerPropertyName));
        }

        $currencyCodeReflectionProperty = new ReflectionProperty($entity, $currencyCodePropertyName);
        $currencyCodeReflectionProperty->setAccessible(true);
        $currencyCode = $currencyCodeReflectionProperty->getValue($entity);
        if(is_null($currencyCode) || '' === $currencyCode) {
            // ignore if nullable and currency instance is null
            if($options['nullable']) {
                return $entity;
            }
            throw new InvalidArgumentException(sprintf(self::$nullPropertyExceptionMessage, get_class($entity), $currencyCodePropertyName));
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
