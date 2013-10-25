<?php

namespace Matmar10\Bundle\MoneyBundle\Tests\Fixtures\Entity;

use Matmar10\Money\Entity\Currency as CurrencyEntity;
use Matmar10\Money\Entity\CurrencyPair as CurrencyPairEntity;
use Matmar10\Money\Entity\ExchangeRate as ExchangeRateEntity;
use Matmar10\Money\Entity\Money as MoneyEntity;
use Matmar10\Bundle\MoneyBundle\Annotation as CompositeProperty;

/**
 * @CompositeProperty\Entity
 */
class AnnotatedTestEntity
{

    /**
     * @var \Matmar10\Money\Entity\Currency
     *
     * @CompositeProperty\Currency(currencyCode="exampleCurrencyCode")
     */
    protected $exampleCurrency;
    protected $exampleCurrencyCode;

    /**
     * @var \Matmar10\Bundle\MoneyBundle\Annotation\Money
     *
     * @CompositeProperty\Money(amountInteger="exampleMoneyAmountInteger",
     *      currencyCode="exampleMoneyCurrencyCode")
     */
    protected $exampleMoney;
    protected $exampleMoneyAmountInteger;
    protected $exampleMoneyCurrencyCode;

    /**
     * @var \Matmar10\Money\Entity\CurrencyPair
     *
     * @CompositeProperty\CurrencyPair(fromCurrencyCode="exampleCurrencyPairFromCurrencyCode",
     *      toCurrencyCode="exampleCurrencyPairToCurrencyCode")
     */
    protected $exampleCurrencyPair;
    protected $exampleCurrencyPairFromCurrencyCode;
    protected $exampleCurrencyPairToCurrencyCode;

    /**
     * @var \Matmar10\Money\Entity\ExchangeRate
     *
     * @CompositeProperty\ExchangeRate(fromCurrencyCode="exampleExchangeRateFromCurrencyCode",
     *      toCurrencyCode="exampleExchangeRateToCurrencyCode",
     *      multiplier="exampleExchangeRateMultiplier")
     */
    protected $exampleExchangeRate;
    protected $exampleExchangeRateFromCurrencyCode;
    protected $exampleExchangeRateToCurrencyCode;
    protected $exampleExchangeRateMultiplier;

    /**
     * @var \Matmar10\Money\Entity\ExchangeRate
     *
     * @CompositeProperty\ExchangeRate(fromCurrencyCode="exampleNullableExchangeRateFromCurrencyCode",
     *      toCurrencyCode="exampleNullableExchangeRateToCurrencyCode",
     *      multiplier="exampleNullableExchangeRateMultiplier",
     *      nullable=true)
     */
    protected $exampleNullableExchangeRate;
    protected $exampleNullableExchangeRateFromCurrencyCode;
    protected $exampleNullableExchangeRateToCurrencyCode;
    protected $exampleNullableExchangeRateMultiplier;

    /**
     * @var \Matmar10\Money\Entity\Currency
     *
     * @CompositeProperty\Currency()
     */
    protected $exampleCurrencyUsingDefaultMap;

    /**
     * @var \Matmar10\Money\Entity\Money
     *
     * @CompositeProperty\Money()
     */
    protected $exampleMoneyUsingDefaultMap;

    /**
     * @var \Matmar10\Money\Entity\CurrencyPair
     *
     * @CompositeProperty\CurrencyPair()
     */
    protected $exampleCurrencyPairUsingDefaultMap;

    /**
     * @var \Matmar10\Money\Entity\ExchangeRate
     *
     * @CompositeProperty\ExchangeRate()
     */
    protected $exampleExchangeRateUsingDefaultMap;


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

    /**
     * @param \Matmar10\Money\Entity\ExchangeRate $exampleNullableExchangeRate
     */
    public function setExampleNullableExchangeRate($exampleNullableExchangeRate)
    {
        $this->exampleNullableExchangeRate = $exampleNullableExchangeRate;
    }

    /**
     * @return \Matmar10\Money\Entity\ExchangeRate
     */
    public function getExampleNullableExchangeRate()
    {
        return $this->exampleNullableExchangeRate;
    }

    public function setExampleNullableExchangeRateFromCurrencyCode($exampleNullableExchangeRateFromCurrencyCode)
    {
        $this->exampleNullableExchangeRateFromCurrencyCode = $exampleNullableExchangeRateFromCurrencyCode;
    }

    public function getExampleNullableExchangeRateFromCurrencyCode()
    {
        return $this->exampleNullableExchangeRateFromCurrencyCode;
    }

    public function setExampleNullableExchangeRateMultiplier($exampleNullableExchangeRateMultiplier)
    {
        $this->exampleNullableExchangeRateMultiplier = $exampleNullableExchangeRateMultiplier;
    }

    public function getExampleNullableExchangeRateMultiplier()
    {
        return $this->exampleNullableExchangeRateMultiplier;
    }

    public function setExampleNullableExchangeRateToCurrencyCode($exampleNullableExchangeRateToCurrencyCode)
    {
        $this->exampleNullableExchangeRateToCurrencyCode = $exampleNullableExchangeRateToCurrencyCode;
    }

    public function getExampleNullableExchangeRateToCurrencyCode()
    {
        return $this->exampleNullableExchangeRateToCurrencyCode;
    }

    /**
     * @param \Matmar10\Money\Entity\Currency $exampleCurrencyUsingDefaultMap
     */
    public function setExampleCurrencyUsingDefaultMap($exampleCurrencyUsingDefaultMap)
    {
        $this->exampleCurrencyUsingDefaultMap = $exampleCurrencyUsingDefaultMap;
    }

    /**
     * @return \Matmar10\Money\Entity\Currency
     */
    public function getExampleCurrencyUsingDefaultMap()
    {
        return $this->exampleCurrencyUsingDefaultMap;
    }

    /**
     * @param \Matmar10\Money\Entity\CurrencyPair $exampleCurrencyPairUsingDefaultMap
     */
    public function setExampleCurrencyPairUsingDefaultMap($exampleCurrencyPairUsingDefaultMap)
    {
        $this->exampleCurrencyPairUsingDefaultMap = $exampleCurrencyPairUsingDefaultMap;
    }

    /**
     * @return \Matmar10\Money\Entity\CurrencyPair
     */
    public function getExampleCurrencyPairUsingDefaultMap()
    {
        return $this->exampleCurrencyPairUsingDefaultMap;
    }

    /**
     * @param \Matmar10\Money\Entity\ExchangeRate $exampleExchangeRateUsingDefaultMap
     */
    public function setExampleExchangeRateUsingDefaultMap($exampleExchangeRateUsingDefaultMap)
    {
        $this->exampleExchangeRateUsingDefaultMap = $exampleExchangeRateUsingDefaultMap;
    }

    /**
     * @return \Matmar10\Money\Entity\ExchangeRate
     */
    public function getExampleExchangeRateUsingDefaultMap()
    {
        return $this->exampleExchangeRateUsingDefaultMap;
    }

    /**
     * @param \Matmar10\Money\Entity\Money $exampleMoneyUsingDefaultMap
     */
    public function setExampleMoneyUsingDefaultMap($exampleMoneyUsingDefaultMap)
    {
        $this->exampleMoneyUsingDefaultMap = $exampleMoneyUsingDefaultMap;
    }

    /**
     * @return \Matmar10\Money\Entity\Money
     */
    public function getExampleMoneyUsingDefaultMap()
    {
        return $this->exampleMoneyUsingDefaultMap;
    }
}
