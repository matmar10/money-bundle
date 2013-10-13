<?php

namespace Matmar10\Bundle\MoneyBundle\Mapper;

use InvalidArgumentException;
use Matmar10\Bundle\MoneyBundle\Annotation\MappedPropertyAnnotationInterface;
use Matmar10\Bundle\MoneyBundle\Exception\NullFieldMappingException;
use Matmar10\Bundle\MoneyBundle\Mapper\DefaultMapper;
use Matmar10\Bundle\MoneyBundle\Mapper\EntityFieldMapperInterface;
use Matmar10\Bundle\MoneyBundle\Service\CurrencyManager;
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
        $annotation->init();
        $mappedProperties = $annotation->getMap();

        // get the currency code from the currency instance
        $reflectionProperty->setAccessible(true);
        /**
         * @var $currencyInstance \Matmar10\Money\Entity\Currency
         */
        $currencyInstance = $reflectionProperty->getValue($entity);

        // ignore if nullable and currency instance is null
        if(is_null($currencyInstance)) {
            $options = $annotation->getOptions();
            if($options['nullable']) {
                return $entity;
            }
            throw new NullFieldMappingException(sprintf("Mapped property '%s' cannot be null (to allow null value, use nullable=true)", $reflectionProperty->getName()));
        }

        $currencyCode = $currencyInstance->getCurrencyCode();
        if(is_null($currencyCode)) {
            throw new NullFieldMappingException(sprintf('Cannot apply pre persist property mapping for %s instance: required field %s is null', get_class($entity), 'currencyCode'));
        }

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

        if(is_null($currencyCode) || '' === $currencyCode) {
            // ignore if nullable and currency instance is null
            $options = $annotation->getOptions();
            if($options['nullable']) {
                return $entity;
            }
            throw new NullFieldMappingException(sprintf('Cannot apply post persist property mapping for %s instance: required field %s is null or blank', get_class($entity), $currencyCodePropertyName));
        }

        // build the currency instance from the currency manager using provided code
        $currencyInstance = $this->currencyManager->getCurrency($currencyCode);

        // set the currency instance on the original entities field
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($entity, $currencyInstance);

        return $entity;
    }
}
