<?php

namespace Lmh\Bundle\MoneyBundle\Tests\Fixtures\Entity;

use Matmar10\Money\Entity\Currency as CurrencyEntity;
use Matmar10\Money\Entity\Money as MoneyEntity;
use Lmh\Bundle\MoneyBundle\Annotation\Currency;
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
}
