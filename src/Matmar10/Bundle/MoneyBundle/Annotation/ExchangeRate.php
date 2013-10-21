<?php

namespace Matmar10\Bundle\MoneyBundle\Annotation;

use Matmar10\Bundle\MoneyBundle\Annotation\BaseCompositeProperty;
use Matmar10\Bundle\MoneyBundle\Annotation\CompositeProperty;

/**
 * ExchangeRate annotation
 *
 * @bundle matmar10-money-bundle
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
class ExchangeRate extends BaseCompositeProperty implements CompositeProperty
{
    /**
     * @var string
     */
    public $fromCurrencyCode;

    /**
     * @var string
     */
    public $toCurrencyCode;

    /**
     * @var float
     */
    public $multiplier;

    /**
     * {inheritDoc}
     */
    public function getClass()
    {
        return '\\Matmar10\\Money\\Entity\\ExchangeRate';
    }

    /**
     * {inheritDoc}
     */
    public function getMap()
    {
        return array(
            'fromCurrencyCode' => $this->fromCurrencyCode,
            'toCurrencyCode' => $this->toCurrencyCode,
            'multiplier' => $this->multiplier,
        );
    }
}
