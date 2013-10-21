<?php

namespace Matmar10\Bundle\MoneyBundle\Tests\Fixtures\Entity;

use Matmar10\Money\Entity\Currency as CurrencyEntity;
use Matmar10\Money\Entity\CurrencyPair as CurrencyPairEntity;
use Matmar10\Money\Entity\ExchangeRate as ExchangeRateEntity;
use Matmar10\Money\Entity\Money as MoneyEntity;
use Matmar10\Bundle\MoneyBundle\Annotation as CPS;

/**
 * @CPS\Entity
 */
class ImproperlyCurrencyAnnotatedTestEntity
{

    /**
     * @var \Matmar10\Money\Entity\Currency
     *
     * @CPS\Currency(curencyCde="exampleCurrencyCode")
     */
    protected $exampleCurrency;
    protected $exampleCurrencyCode;

}
