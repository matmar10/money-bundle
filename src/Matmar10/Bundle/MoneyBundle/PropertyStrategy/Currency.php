<?php

namespace Matmar10\Bundle\MoneyBundle\PropertyStrategy;

use InvalidArgumentException;
use Matmar10\Bundle\MoneyBundle\Annotation\CompositeProperty;
use Matmar10\Bundle\MoneyBundle\Exception\NullPropertyMappingException;
use Matmar10\Bundle\MoneyBundle\PropertyStrategy\CompositePropertyStrategy;
use Matmar10\Bundle\MoneyBundle\Service\CurrencyManager;
use ReflectionObject;
use ReflectionProperty;

class Currency implements CompositePropertyStrategy
{

    /**
     * @var \Matmar10\Bundle\MoneyBundle\Service\CurrencyManager
     */
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
         * @var $annotation \Matmar10\Bundle\MoneyBundle\Annotation\Currency
         */

        // get the currency code from the currency instance
        $reflectionProperty->setAccessible(true);

        /**
         * @var $currencyInstance \Matmar10\Money\Entity\Currency
         */
        $currencyInstance = $reflectionProperty->getValue($entity);

        // ignore if nullable and currency instance is null
        if(is_null($currencyInstance)) {
            if($annotation->getNullable()) {
                return;
            }
            throw new NullPropertyMappingException();
        }

        $currencyCode = $currencyInstance->getCurrencyCode();
        if(is_null($currencyCode)) {
            throw new NullPropertyMappingException();
        }

        // set the currency code to the specified field name
        $currencyCodeReflectionProperty = new ReflectionProperty($entity, $annotation->getCurrencyCode());
        $currencyCodeReflectionProperty->setAccessible(true);
        $currencyCodeReflectionProperty->setValue($entity, $currencyCode);
    }

    /**
     * {inheritDoc}
     */
    public function composeCompositeProperty(&$entity, ReflectionProperty $reflectionProperty, CompositeProperty $annotation)
    {
        /**
         * @var $annotation \Matmar10\Bundle\MoneyBundle\Annotation\Currency
         */
        $currencyCodeReflectionProperty = new ReflectionProperty($entity, $annotation->getCurrencyCode());
        $currencyCodeReflectionProperty->setAccessible(true);
        $currencyCode = $currencyCodeReflectionProperty->getValue($entity);

        if(is_null($currencyCode) || '' === $currencyCode) {
            // ignore if nullable and currency instance is null
            if($annotation->getNullable()) {
                return;
            }
            throw new NullPropertyMappingException();
        }

        // build the currency instance from the currency manager using provided code
        $currencyInstance = $this->currencyManager->getCurrency($currencyCode);

        // set the currency instance on the original entities field
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($entity, $currencyInstance);
    }
}
