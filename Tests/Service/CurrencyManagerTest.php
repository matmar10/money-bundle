<?php

namespace Lmh\Bundle\MoneyBundle\Tests\Validator\Constraints;

use Lmh\Bundle\MoneyBundle\Entity\Currency;
use Lmh\Bundle\MoneyBundle\Entity\CurrencyPair;
use Lmh\Bundle\MoneyBundle\Entity\Money;
use Lmh\Bundle\MoneyBundle\Validator\Constraints\CurrencyCode;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CurrencyManagerTest extends WebTestCase
{
    
    protected $manager;

    private function getKernel()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        return $kernel;
    }

    public function setUp()
    {
        $kernel = $this->getKernel();
        $this->manager = $kernel->getContainer()->get('lmh_money.currency_manager');
    }

    /**
     * @dataProvider provideTestGetCodeForCountry
     */
    public function testGetCodeForCountry($code, $country)
    {
        $this->assertEquals($code, $this->manager->getCode($country));
    }

    public function provideTestGetCodeForCountry()
    {
        return array(
            array('USD', 'US', ),
            array('GBP', 'GB', ),
            array('EUR', 'FR', ),
            array('MAD', 'MA', ),
            array('012', '01', ),
            array('012', '02', ),
            array('034', '03', ),
            array('034', '04', ),
        );
    }

    /**
     * @dataProvider provideTestGetCodeFromCode
     */
    public function testGetCodeFromCode($currencyCode)
    {
        $this->assertEquals($currencyCode, $this->manager->getCode($currencyCode));
    }

    public function provideTestGetCodeFromCode()
    {
        return array(
            array(
                'USD',
            ),
            array(
                'GBP',
            ),
            array(
                'EUR',
            ),
            array(
                'MAD',
            ),
            array(
                '012',
            ),
            array(
                '034',
            ),
        );
    }

    /**
     * @expectedException Lmh\Bundle\MoneyBundle\Exception\InvalidArgumentException
     */
    public function testGetCodeForInvalidArgument()
    {
        $this->manager->getCode('FOO');
    }

    /**
     * @dataProvider provideTestGetCurrencyForCountry
     */
    public function testGetCurrencyForCountry(Currency $currency, $countryCode)
    {
        $this->assertEquals($currency, $this->manager->getCurrency($countryCode));
    }

    public function provideTestGetCurrencyForCountry()
    {
        return array(
            array(
                new Currency('USD', 2, 2, '&#36;'),
                'US',
            ),
            array(
                new Currency('GBP', 2, 2, '&#163;'),
                'GB',
            ),
            array(
                new Currency('EUR', 2, 2, '&#8364;'),
                'FR',
            ),
            array(
                new Currency('MAD', 2, 2),
                'MA',
            ),

            // dummy currencies to test the semantic configuration
            array(
                new Currency('012', 1, 2),
                '012'
            ),
            array(
                new Currency('034', 3, 4),
                '034'
            ),
        );
    }

    /**
     * @dataProvider provideTestGetCurrencyForCurrencyCode
     */
    public function testGetCurrencyForCurrencyCode(Currency $currency, $code)
    {
        $this->assertEquals($currency, $this->manager->getCurrency($code));
    }

    public function provideTestGetCurrencyForCurrencyCode()
    {
        return array(
            array(
                new Currency('USD', 2, 2, '&#36;'),
                'USD',
            ),
            array(
                new Currency('GBP', 2, 2, '&#163;'),
                'GBP',
            ),
            array(
                new Currency('EUR', 2, 2, '&#8364;'),
                'EUR',
            ),
            array(
                new Currency('MAD', 2, 2),
                'MAD',
            ),

            // dummy currencies to test the semantic configuration
            array(
                new Currency('012', 1, 2),
                '012'
            ),
            array(
                new Currency('034', 3, 4),
                '034'
            ),
        );
    }

    /**
     * @expectedException Lmh\Bundle\MoneyBundle\Exception\InvalidArgumentException
     */
    public function testGetCurrencyForInvalidArgument()
    {
        $this->manager->getCurrency('FOO');
    }

    /**
     * @dataProvider providerTestGetMoneyForCountry
     */
    public function testGetMoneyForCountry(Money $money, $country)
    {
        $this->assertEquals($money, $this->manager->getMoney($country));
    }

    public function providerTestGetMoneyForCountry()
    {
        return array(
            array(
                new Money(new Currency('USD', 2, 2, '&#36;')),
                'US',
            ),
            array(
                new Money(new Currency('GBP', 2, 2, '&#163;')),
                'GB',
            ),
            array(
                new Money(new Currency('EUR', 2, 2, '&#8364;')),
                'FR',
            ),
            array(
                new Money(new Currency('MAD', 2, 2)),
                'MA',
            ),
            // dummy currencies to test the semantic configuration
            array(
                new Money(new Currency('012', 1, 2)),
                '012'
            ),
            array(
                new Money(new Currency('034', 3, 4)),
                '034'
            ),
        );
    }

    /**
     * @dataProvider provideTestGetMoneyForCurrencyCode
     */
    public function testGetMoneyForCurrencyCode(Money $money, $currencyCode)
    {
        $this->assertEquals($money, $this->manager->getMoney($currencyCode));
    }

    public function provideTestGetMoneyForCurrencyCode()
    {
        return array(
            array(
                new Money(new Currency('USD', 2, 2, '&#36;')),
                'USD',
            ),
            array(
                new Money(new Currency('GBP', 2, 2, '&#163;')),
                'GBP',
            ),
            array(
                new Money(new Currency('EUR', 2, 2, '&#8364;')),
                'EUR',
            ),
            array(
                new Money(new Currency('MAD', 2, 2)),
                'MAD'
            ),

            // dummy currencies to test the semantic configuration
            array(
                new Money(new Currency('012', 1, 2)),
                '012'
            ),
            array(
                new Money(new Currency('034', 3, 4)),
                '034'
            ),
            /* TODO: get XML based configuration working and re-enable these tests
            array(
                new Money(new Currency('034', 5, 6)),
                '056'
            ),
            array(
                new Money(new Currency('034', 7, 8)),
                '078'
            ),
            */
        );
    }

    /**
     * @expectedException Lmh\Bundle\MoneyBundle\Exception\InvalidArgumentException
     */
    public function testGetMoneyForInvalidArgument()
    {
        $this->manager->getMoney('FOO');
    }


    /**
     * @dataProvider provideTestGetCurrencyPairForCountries
     */
    public function testGetCurrencyPairForCountries($expectedPair, $fromCountry, $toCountry, $rate)
    {
        $this->assertEquals($expectedPair, $this->manager->getCurrencyPair($fromCountry, $toCountry, $rate));
    }

    public function provideTestGetCurrencyPairForCountries()
    {
        return array(
            array(
                new CurrencyPair(new Currency('USD', 2, 2, '&#36;'), new Currency('GBP', 2, 2, '&#163;'), 1.7),
                'US',
                'GB',
                1.7,
            ),
            array(
                new CurrencyPair(new Currency('GBP', 2, 2, '&#163;'), new Currency('EUR', 2, 2, '&#8364;'), 0.9),
                'GB',
                'FR',
                0.9,
            ),
            array(
                new CurrencyPair(new Currency('012', 1, 2, ''), new Currency('034', 3, 4, ''), 5.6),
                '01',
                '03',
                5.6,
            ),
        );
    }

    /**
     * @expectedException Lmh\Bundle\MoneyBundle\Exception\InvalidArgumentException
     */
    public function testGetCurrencyPairForCountriesInvalidArgument()
    {
        $test = new CurrencyPair(new Currency('BLAH', 5, 2, '&#36;'), new Currency('FOO', 5, 2, '&#163;'), 1.7);
    }

    /**
     * @dataProvider provideTestAddCurrency
     */
    public function testAddCurrency($currency, $expectException = false)
    {
        $this->manager->addCurrency($currency);
        $foundCurrency = $this->manager->getCurrency($currency->getCurrencyCode());
        $this->assertEquals($currency, $foundCurrency);
    }

    public function provideTestAddCurrency()
    {
        return array(
            array(
                new Currency('XXX', 1, 1, 'x'),
            ),
            array(
                new Currency('AAA', 1, 1, 'a'),
            )
        );
    }
}
