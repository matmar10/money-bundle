<?php

namespace Matmar10\Bundle\MoneyBundle\Tests\Fixtures\Entity;

use Matmar10\Money\Entity\Currency as CurrencyEntity;
use Matmar10\Money\Entity\CurrencyPair as CurrencyPairEntity;
use Matmar10\Money\Entity\ExchangeRate as ExchangeRateEntity;
use Matmar10\Money\Entity\Money as MoneyEntity;
use Matmar10\Bundle\MoneyBundle\Annotation\Currency;
use Matmar10\Bundle\MoneyBundle\Annotation\CurrencyPair;
use Matmar10\Bundle\MoneyBundle\Annotation\ExchangeRate;
use Matmar10\Bundle\MoneyBundle\Annotation\Money;

class AnnotatedTestEntity
{

    /**
     * @var \Matmar10\Money\Entity\Currency
     *
     * @Currency(currencyCode="exampleCurrencyCode")
     */
    protected $exampleCurrency;
    protected $exampleCurrencyCode;

    /**
     * @var \Matmar10\Bundle\MoneyBundle\Annotation\Money
     *
     * @Money(amountInteger="exampleMoneyAmountInteger",
     *      currencyCode="exampleMoneyCurrencyCode")
     */
    protected $exampleMoney;
    protected $exampleMoneyAmountInteger;
    protected $exampleMoneyCurrencyCode;

    /**
     * @var \Matmar10\Money\Entity\CurrencyPair
     *
     * @CurrencyPair(fromCurrencyCode="exampleCurrencyPairFromCurrencyCode",
     *      toCurrencyCode="exampleCurrencyPairToCurrencyCode")
     */
    protected $exampleCurrencyPair;
    protected $exampleCurrencyPairFromCurrencyCode;
    protected $exampleCurrencyPairToCurrencyCode;

    /**
     * @var \Matmar10\Money\Entity\ExchangeRate
     *
     * @ExchangeRate (fromCurrencyCode="exampleExchangeRateFromCurrencyCode",
     *      toCurrencyCode="exampleExchangeRateToCurrencyCode",
     *      multiplier="exampleExchangeRateMultiplier")
     */
    protected $exampleExchangeRate;
    protected $exampleExchangeRateFromCurrencyCode;
    protected $exampleExchangeRateToCurrencyCode;
    protected $exampleExchangeRateMultiplier;

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

    public function setExampleCurrencyPair(CurrencyPairEntity $exampleCurrencyPair)
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

    public function setExampleCurrencyPairToCurrencyCode($exampleCurrencyPairToCurrencyCode)
    {
        $this->exampleCurrencyPairToCurrencyCode = $exampleCurrencyPairToCurrencyCode;
    }

    public function getExampleCurrencyPairToCurrencyCode()
    {
        return $this->exampleCurrencyPairToCurrencyCode;
    }

    public function setExampleExchangeRate(ExchangeRateEntity $exampleExchangeRate)
    {
        $this->exampleExchangeRate = $exampleExchangeRate;
    }

    public function getExampleExchangeRate()
    {
        return $this->exampleExchangeRate;
    }

    public function setExampleExchangeRateFromCurrencyCode($exampleExchangeRateFromCurrencyCode)
    {
        $this->exampleExchangeRateFromCurrencyCode = $exampleExchangeRateFromCurrencyCode;
    }

    public function getExampleExchangeRateFromCurrencyCode()
    {
        return $this->exampleExchangeRateFromCurrencyCode;
    }

    public function setExampleExchangeRateToCurrencyCode($exampleExchangeRateToCurrencyCode)
    {
        $this->exampleExchangeRateToCurrencyCode = $exampleExchangeRateToCurrencyCode;
    }

    public function getExampleExchangeRateToCurrencyCode()
    {
        return $this->exampleExchangeRateToCurrencyCode;
    }

    public function setExampleExchangeRateMultiplier($exampleExchangeRateMultiplier)
    {
        $this->exampleExchangeRateMultiplier = $exampleExchangeRateMultiplier;
    }

    public function getExampleExchangeRateMultiplier()
    {
        return $this->exampleExchangeRateMultiplier;
    }
}
