<?php

namespace Matmar10\Bundle\MoneyBundle\PropertyStrategy;

use InvalidArgumentException;
use Matmar10\Bundle\MoneyBundle\Annotation\CompositeProperty;
use Matmar10\Bundle\MoneyBundle\Exception\NullPropertyException;
use Matmar10\Bundle\MoneyBundle\PropertyStrategy\CompositePropertyStrategy;
use Matmar10\Bundle\MoneyBundle\PropertyStrategy\BaseStrategy;
use Matmar10\Bundle\MoneyBundle\Service\CurrencyManager;
use ReflectionObject;
use ReflectionProperty;

/**
 * {inheritDoc}
 */
class ExchangeRate extends BaseStrategy implements CompositePropertyStrategy
{

    /**
     * @var \Matmar10\Bundle\MoneyBundle\Service\CurrencyManager
     */
    protected $currencyManager;

    public function __construct(CurrencyManager $currencyManager)
    {
        $this->currencyManager = $currencyManager;
    }

    public function flattenCompositeProperty(&$entity, ReflectionProperty $reflectionProperty, CompositeProperty $annotation)
    {
        /**
         * @var $annotation \Matmar10\Bundle\MoneyBundle\Annotation\ExchangeRate
         * @var $instance \Matmar10\Money\Entity\ExchangeRate
         */
        $instance = $this->getCompoundPropertyInstance($entity, $reflectionProperty, $annotation);
        if(!$instance) {
            return;
        }

        $this->setValues($entity, $reflectionProperty, $annotation, array(
            'fromCurrencyCode' => $instance->getFromCurrency()->getCurrencyCode(),
            'toCurrencyCode' => $instance->getToCurrency()->getCurrencyCode(),
            'multiplier' => $instance->getMultiplier(),
        ));
    }

    public function composeCompositeProperty(&$entity, ReflectionProperty $reflectionProperty, CompositeProperty $annotation)
    {
        /**
         * @var $annotation \Matmar10\Bundle\MoneyBundle\Annotation\ExchangeRate
         */
        $values = $this->getValues($entity, $reflectionProperty, $annotation);
        if(is_null($values['fromCurrencyCode'])
            || is_null($values['toCurrencyCode'])
            || is_null($values['multiplier'])) {
            return;
        }

        // build the currency instance from the currency manager using provided code
        $fromCurrency = $this->currencyManager->getCurrency($values['fromCurrencyCode']);
        $toCurrency = $this->currencyManager->getCurrency($values['toCurrencyCode']);

        $className = $annotation->getClass();
        $exchangeRateInstance = new $className($fromCurrency, $toCurrency, $values['multiplier']);

        // set the currency instance on the original entities field
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($entity, $exchangeRateInstance);
    }
}
