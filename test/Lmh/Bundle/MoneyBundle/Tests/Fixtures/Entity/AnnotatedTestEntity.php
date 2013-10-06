<?php

namespace Lmh\Bundle\MoneyBundle\Tests\Fixtures\Entity;

use Matmar10\Money\Entity\Currency as CurrencyEntity;
use Matmar10\Money\Entity\CurrencyPair as CurrencyPairEntity;
use Matmar10\Money\Entity\Money as MoneyEntity;
use Lmh\Bundle\MoneyBundle\Annotation\Currency;
use Lmh\Bundle\MoneyBundle\Annotation\CurrencyPair;
use Lmh\Bundle\MoneyBundle\Annotation\Money;

class AnnotatedTestEntity
{

    /**
     * @Currency(currencyCode="exampleCurrencyCode")
     */
    protected $exampleCurrency;
    protected $exampleCurrencyCode;

    /**
     * @Money(amountInteger="exampleMoneyAmountInteger", currencyCode="exampleMoneyCurrencyCode")
     */
    protected $exampleMoney;
    protected $exampleMoneyAmountInteger;
    protected $exampleMoneyCurrencyCode;

    /**
     * @CurrencyPair(fromCurrencyCode="exampleCurrencyPairFromCurrencyCode",
     *      toCurrencyCode="exampleCurrencyPairToCurrencyCode",
     *      multiplier="exampleCurrencyPairMultiplier")
     */
    protected $exampleCurrencyPair;
    protected $exampleCurrencyPairFromCurrencyCode;
    protected $exampleCurrencyPairToCurrencyCode;
    protected $exampleCurrencyPairMultiplier;

    public function setExampleCurrency(CurrencyEntity $exampleCurrency)
    {
        $this->exampleCurrency = $exampleCurrency;
    }

    public function getExampleCurrency()
    {
        return $this->exampleCurrency;
    }

    public function setExampleCurrencyCode($exampleCurrencyCode)
    {
        $this->exampleCurrencyCode = $exampleCurrencyCode;
    }

    public function getExampleCurrencyCode()
    {
        return $this->exampleCurrencyCode;
    }

    public function setExampleMoney(MoneyEntity $exampleMoney)
    {
        $this->exampleMoney = $exampleMoney;
    }

    public function getExampleMoney()
    {
        return $this->exampleMoney;
    }

    public function setExampleMoneyAmountInteger($exampleMoneyAmountInteger)
    {
        $this->exampleMoneyAmountInteger = $exampleMoneyAmountInteger;
    }

    public function getExampleMoneyAmountInteger()
    {
        return $this->exampleMoneyAmountInteger;
    }

    public function setExampleMoneyCurrencyCode($exampleMoneyCurrencyCode)
    {
        $this->exampleMoneyCurrencyCode = $exampleMoneyCurrencyCode;
    }

    public function getExampleMoneyCurrencyCode()
    {
        return $this->exampleMoneyCurrencyCode;
    }

    public function setExampleCurrencyPair($exampleCurrencyPair)
    {
        $this->exampleCurrencyPair = $exampleCurrencyPair;
    }

    public function getExampleCurrencyPair()
    {
        return $this->exampleCurrencyPair;
    }

    public function setExampleCurrencyPairFromCurrencyCode($exampleCurrencyPairFromCurrencyCode)
    {
        $this->exampleCurrencyPairFromCurrencyCode = $exampleCurrencyPairFromCurrencyCode;
    }

    public function getExampleCurrencyPairFromCurrencyCode()
    {
        return $this->exampleCurrencyPairFromCurrencyCode;
    }

    public function setExampleCurrencyPairMultiplier($exampleCurrencyPairMultiplier)
    {
        $this->exampleCurrencyPairMultiplier = $exampleCurrencyPairMultiplier;
    }

    public function getExampleCurrencyPairMultiplier()
    {
        return $this->exampleCurrencyPairMultiplier;
    }

    public function setExampleCurrencyPairToCurrencyCode($exampleCurrencyPairToCurrencyCode)
    {
        $this->exampleCurrencyPairToCurrencyCode = $exampleCurrencyPairToCurrencyCode;
    }

    public function getExampleCurrencyPairToCurrencyCode()
    {
        return $this->exampleCurrencyPairToCurrencyCode;
    }
}
