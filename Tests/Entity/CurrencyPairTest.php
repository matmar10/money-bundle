<?php

namespace Lmh\Bundle\MoneyBundle\Tests\Entity;

use Lmh\Bundle\MoneyBundle\Entity\CurrencyPair;
use Lmh\Bundle\MoneyBundle\Entity\Currency;
use Lmh\Bundle\MoneyBundle\Entity\Money;
use PHPUnit_Framework_TestCase as TestCase;

class CurrencyPairTest extends TestCase
{
    protected $usdCode;
    protected $usd;
    protected $usdMoney;
    protected $eurCode;
    protected $eur;
    protected $eurMoney;
    protected $rate;

    public function setUp()
    {
        $this->usd = new Currency('USD', 5, 2);
        $this->usdMoney = new Money($this->usd);

        $this->eur = new Currency('EUR', 5, 2);
        $this->eurMoney = new Money($this->eur);

        $this->rate = new CurrencyPair($this->usd, $this->eur, 1.5);
    }

    public function testConvert()
    {
        $usd = clone $this->usdMoney;
        $usd->setAmountFloat(10);

        $rate = clone $this->rate;

        $eur = clone $this->eurMoney;
        $eur->setAmountFloat(15);

        $this->assertEquals($eur, $rate->convert($usd));
        $this->assertEquals($usd, $rate->convert($eur));
    }

    /**
     * @expectedException Lmh\Bundle\MoneyBundle\Exception\InvalidArgumentException
     */
    public function testCannotConvertMismatchedCurrency()
    {
        $jpn = new Currency('JPY', 5, 2);
        $jpnAmount = new Money($jpn);
        $jpnAmount->setAmountFloat(100);

        $this->rate->convert($jpnAmount);
    }

}