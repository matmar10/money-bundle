<?php

namespace Matmar10\Bundle\MoneyBundle\PropertyStrategy;

use InvalidArgumentException;
use Matmar10\Bundle\MoneyBundle\Annotation\CompositeProperty;
use Matmar10\Bundle\MoneyBundle\Exception\NullPropertyException;
use Matmar10\Bundle\MoneyBundle\PropertyStrategy\BaseStrategy;
use Matmar10\Bundle\MoneyBundle\PropertyStrategy\CompositePropertyStrategy;
use Matmar10\Bundle\MoneyBundle\Service\CurrencyManager;
use ReflectionObject;
use ReflectionProperty;

/**
 * {inheritDoc}
 */
class Money extends BaseStrategy implements CompositePropertyStrategy
{

    protected static $nullPropertyExceptionMessage = 'Cannot apply entity mapping for %s instance: required property %s is null or blank';

    protected $currencyManager;

    public function __construct(CurrencyManager $currencyManager)
    {
        $this->currencyManager = $currencyManager;
    }

    public function flattenCompositeProperty(&$entity, ReflectionProperty $reflectionProperty, CompositeProperty $annotation)
    {
        /**
         * @var $annotation \Matmar10\Bundle\MoneyBundle\Annotation\Money
         * @var $instance \Matmar10\Money\Entity\Money
         */
        $instance = $this->getCompoundPropertyInstance($entity, $reflectionProperty, $annotation);
        if(!$instance) {
            return;
        }

        $this->setValues($entity, $reflectionProperty, $annotation, array(
            'currencyCode' => $instance->getCurrency()->getCurrencyCode(),
            'amountInteger' => $instance->getAmountInteger(),
        ));
    }

    public function composeCompositeProperty(&$entity, ReflectionProperty $reflectionProperty, CompositeProperty $annotation)
    {
        $values = $this->getValues($entity, $reflectionProperty, $annotation);
        if(is_null($values['currencyCode'])
            || is_null($values['amountInteger'])) {
            return;
        }

        // build the currency instance from the currency manager using provided code
        $moneyInstance = $this->currencyManager->getMoney($values['currencyCode']);
        $moneyInstance->setAmountInteger($values['amountInteger']);

        // set the currency instance on the original entities field
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($entity, $moneyInstance);
    }
}
