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
class CurrencyPair extends BaseStrategy implements CompositePropertyStrategy
{

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
         * @var $annotation \Matmar10\Bundle\MoneyBundle\Annotation\CurrencyPair
         * @var $instance \Matmar10\Money\Entity\CurrencyPair
         */
        $instance = $this->getCompoundPropertyInstance($entity, $reflectionProperty, $annotation);
        if(!$instance) {
            return;
        }

        $this->setValues($entity, $reflectionProperty, $annotation, array(
            'fromCurrencyCode' => $instance->getFromCurrency()->getCurrencyCode(),
            'toCurrencyCode' => $instance->getToCurrency()->getCurrencyCode(),
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
        if(is_null($values['fromCurrencyCode'])
            || is_null($values['toCurrencyCode'])) {
            return;
        }

        // build the currency instance from the currency manager using provided code
        $className = $annotation->getClass();
        $fromCurrency = $this->currencyManager->getCurrency($values['fromCurrencyCode']);
        $toCurrency = $this->currencyManager->getCurrency($values['toCurrencyCode']);

        /**
          * @var $annotation \Matmar10\Bundle\MoneyBundle\Annotation\CurrencyPair
          */
        $currencyPair = new $className($fromCurrency, $toCurrency);

        // set the currency instance on the original entities field
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($entity, $currencyPair);
    }
}
