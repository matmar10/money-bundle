<?php

namespace Matmar10\Bundle\MoneyBundle\PropertyStrategy;

use InvalidArgumentException;
use Matmar10\Bundle\MoneyBundle\Annotation\CompositeProperty;
use Matmar10\Bundle\MoneyBundle\Exception\NullPropertyException;
use Matmar10\Bundle\MoneyBundle\Exception\NullPropertyMappingException;
use Matmar10\Bundle\MoneyBundle\PropertyStrategy\BaseStrategy;
use Matmar10\Bundle\MoneyBundle\PropertyStrategy\CompositePropertyStrategy;
use Matmar10\Bundle\MoneyBundle\Service\CurrencyManager;
use ReflectionException;
use ReflectionObject;
use ReflectionProperty;

class Currency extends BaseStrategy implements CompositePropertyStrategy
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
         * @var $instance \Matmar10\Money\Entity\Currency
         */
        $instance = $this->getCompoundPropertyInstance($entity, $reflectionProperty, $annotation);
        if(!$instance) {
            return;
        }

        $this->setValues($entity, $reflectionProperty, $annotation, array(
            'currencyCode' => $instance->getCurrencyCode(),
        ));
    }

    /**
     * {inheritDoc}
     */
    public function composeCompositeProperty(&$entity, ReflectionProperty $reflectionProperty, CompositeProperty $annotation)
    {
        /**
         * @var $annotation \Matmar10\Bundle\MoneyBundle\Annotation\Currency
         */
        $values = $this->getValues($entity, $reflectionProperty, $annotation);
        if(is_null($values['currencyCode'])) {
            return;
        }

        // build the currency instance from the currency manager using provided code
        $currencyInstance = $this->currencyManager->getCurrency($values['currencyCode']);

        // set the currency instance on the original entities field
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($entity, $currencyInstance);
    }
}
