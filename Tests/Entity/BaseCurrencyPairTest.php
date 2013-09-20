<?php

namespace Lmh\Bundle\MoneyBundle\Tests\Entity;

use Lmh\Bundle\MoneyBundle\Entity\BaseCurrencyPair;
use Lmh\Bundle\MoneyBundle\Entity\Currency;
use Lmh\Bundle\MoneyBundle\Entity\Money;
use PHPUnit_Framework_TestCase as TestCase;

class BaseCurrencyPairTest extends TestCase
{
    protected $usdCode;
    protected $usd;
    protected $usdMoney;

    protected $eurCode;
    protected $eur;
    protected $eurMoney;

    protected $gbpCode;
    protected $gbp;
    protected $gbpMoney;

    protected $usdEurPair;
    protected $usdGbpPair;

    public function setUp()
    {
        $this->usd = new Currency('USD', 2, 2);
        $this->usdMoney = new Money($this->usd);

        $this->eur = new Currency('EUR', 2, 2);
        $this->eurMoney = new Money($this->eur);

        $this->gbp = new Currency('GBP', 2, 2);
        $this->gbpMoney = new Money($this->gbp);

        $this->usdEurPair = new BaseCurrencyPair($this->usd, $this->eur);
        $this->usdGbpPair = new BaseCurrencyPair($this->usd, $this->gbp);
    }

    /**
     * @dataProvider provideTestEqualsData
     */
    public function testEquals(BaseCurrencyPair $pair1, BaseCurrencyPair $pair2, $expectedResult)
    {
        $this->assertEquals($expectedResult, $pair1->equals($pair2));
    }

    public function provideTestEqualsData()
    {
        $usd = new Currency('USD', 2, 2);
        $usd2 = new Currency('USD', 5, 5);
        $eur = new Currency('EUR', 2, 2);
        $eur2 = new Currency('EUR', 6, 3);
        $gbp = new Currency('GBP', 2, 2);

        return array(
            'identical pairs are equal' => array(
                new BaseCurrencyPair($usd, $eur),
                new BaseCurrencyPair($usd, $eur),
                true,
            ),
            'identical pairs with different precisions are equal' => array(
                new BaseCurrencyPair($usd, $eur),
                new BaseCurrencyPair($usd2, $eur2),
                true,
            ),
            'different pairs are not equal' => array(
                new BaseCurrencyPair($usd, $eur),
                new BaseCurrencyPair($usd, $gbp),
                false,
            ),
        );
    }

    /**
     * @dataProvider provideTestIsInverseData
     */
    public function testIsInverse(BaseCurrencyPair $pair1, BaseCurrencyPair $pair2, $expectedResult)
    {
        $this->assertEquals($expectedResult, $pair1->isInverse($pair2));
    }

    public function provideTestIsInverseData()
    {

        $usd = new Currency('USD', 2, 2);
        $usd2 = new Currency('USD', 5, 5);
        $eur = new Currency('EUR', 2, 2);
        $eur2 = new Currency('EUR', 6, 3);
        $gbp = new Currency('GBP', 2, 2);

        return array(
            'identical pairs are not inverse' => array(
                new BaseCurrencyPair($usd, $eur),
                new BaseCurrencyPair($usd, $eur),
                false,
            ),
            'identical pairs with different precisions are not inverse' => array(
                new BaseCurrencyPair($usd, $eur),
                new BaseCurrencyPair($usd2, $eur2),
                false,
            ),
            'different pairs are not inverse' => array(
                new BaseCurrencyPair($usd, $eur),
                new BaseCurrencyPair($usd, $gbp),
                false,
            ),
            'exact inverse pairs are inverse' => array(
                new BaseCurrencyPair($usd, $eur),
                new BaseCurrencyPair($eur, $usd),
                true,
            ),
            'exact inverse pairs are inverse 2' => array(
                new BaseCurrencyPair($gbp, $eur),
                new BaseCurrencyPair($eur, $gbp),
                true,
            ),
            'inverse pairs with different precisions are still inverse' => array(
                new BaseCurrencyPair($eur, $usd),
                new BaseCurrencyPair($usd2, $eur2),
                true,
            ),
        );
    }

}