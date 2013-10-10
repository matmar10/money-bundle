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

class ImproperlyCurrencyAnnotatedTestEntity
{

    /**
     * @var \Matmar10\Money\Entity\Currency
     *
     * @Currency(curencyCde="exampleCurrencyCode")
     */
    protected $exampleCurrency;
    protected $exampleCurrencyCode;

}
