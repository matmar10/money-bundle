<?php

namespace Matmar10\Bundle\MoneyBundle\Annotation;

use Matmar10\Bundle\MoneyBundle\Annotation\BaseCompositeProperty;
use Matmar10\Bundle\MoneyBundle\Annotation\CompositeProperty;
use Matmar10\Bundle\MoneyBundle\Exception\InvalidArgumentException;

/**
 * Money
 *
 * @bundle matmar10-money-bundle
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Money extends BaseCompositeProperty implements CompositeProperty
{

    /**
     * @var string
     */
    public $currencyCode;

    /**
     * @var integer
     */
    public $amountInteger;

    /**
     * {inheritDoc}
     */
    public function getClass()
    {
        return '\\Matmar10\\Money\\Entity\\Money';
    }

    /**
     * {inheritDoc}
     */
    public function getMap()
    {
        return array(
            'currencyCode' => $this->currencyCode,
            'amountInteger' => $this->amountInteger,
        );
    }

    /**
     * @return int
     */
    public function getAmountInteger()
    {
        return $this->amountInteger;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }
}
