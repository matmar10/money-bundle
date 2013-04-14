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

    public function testGetCodeForCountry()
    {
        $this->assertEquals('USD', $this->manager->getCode('US'));
        $this->assertEquals('GBP', $this->manager->getCode('GB'));
        $this->assertEquals('EUR', $this->manager->getCode('FR'));
        $this->assertEquals('MAD', $this->manager->getCode('MA'));
    }

    public function testGetCodeFromCode()
    {
        $this->assertEquals('USD', $this->manager->getCode('USD'));
        $this->assertEquals('GBP', $this->manager->getCode('GBP'));
        $this->assertEquals('EUR', $this->manager->getCode('EUR'));
        $this->assertEquals('MAD', $this->manager->getCode('MAD'));
    }

    /**
     * @expectedException Lmh\Bundle\MoneyBundle\Exception\InvalidArgumentException
     */
    public function testGetCodeForInvalidArgument()
    {
        $this->manager->getCode('FOO');
    }

    public function testGetCurrencyForCountry()
    {
        $this->assertEquals(new Currency('USD', 5, 2, '&#36;'), $this->manager->getCurrency('US'));
        $this->assertEquals(new Currency('GBP', 5, 2, '&#163;'), $this->manager->getCurrency('GB'));
        $this->assertEquals(new Currency('EUR', 5, 2, '&#8364;'), $this->manager->getCurrency('FR'));
        $this->assertEquals(new Currency('MAD', 5, 2), $this->manager->getCurrency('MA'));
    }

    public function testGetCurrencyForCurrencyCode()
    {
        $this->assertEquals(new Currency('USD', 5, 2, '&#36;'), $this->manager->getCurrency('USD'));
        $this->assertEquals(new Currency('GBP', 5, 2, '&#163;'), $this->manager->getCurrency('GBP'));
        $this->assertEquals(new Currency('EUR', 5, 2, '&#8364;'), $this->manager->getCurrency('EUR'));
        $this->assertEquals(new Currency('MAD', 5, 2), $this->manager->getCurrency('MAD'));
    }

    /**
     * @expectedException Lmh\Bundle\MoneyBundle\Exception\InvalidArgumentException
     */
    public function testGetCurrencyForInvalidArgument()
    {
        $this->manager->getCurrency('FOO');
    }

    public function testGetMoneyForCountry()
    {
        $this->assertEquals(new Money(new Currency('USD', 5, 2, '&#36;')), $this->manager->getMoney('US'));
        $this->assertEquals(new Money(new Currency('GBP', 5, 2, '&#163;')), $this->manager->getMoney('GB'));
        $this->assertEquals(new Money(new Currency('EUR', 5, 2, '&#8364;')), $this->manager->getMoney('FR'));
        $this->assertEquals(new Money(new Currency('MAD', 5, 2)), $this->manager->getMoney('MA'));
    }

    public function testGetMoneyForCurrencyCode()
    {
        $this->assertEquals(new Money(new Currency('USD', 5, 2, '&#36;')), $this->manager->getMoney('USD'));
        $this->assertEquals(new Money(new Currency('GBP', 5, 2, '&#163;')), $this->manager->getMoney('GBP'));
        $this->assertEquals(new Money(new Currency('EUR', 5, 2, '&#8364;')), $this->manager->getMoney('EUR'));
        $this->assertEquals(new Money(new Currency('MAD', 5, 2)), $this->manager->getMoney('MAD'));
    }

    /**
     * @expectedException Lmh\Bundle\MoneyBundle\Exception\InvalidArgumentException
     */
    public function testGetMoneyForInvalidArgument()
    {
        $this->manager->getMoney('FOO');
    }

    public function testGetCurrencyPairForCountries()
    {
        $this->assertEquals(
            new CurrencyPair(new Currency('USD', 5, 2, '&#36;'), new Currency('GBP', 5, 2, '&#163;'), 1.7),
            $this->manager->getCurrencyPair('US', 'GB', 1.7));

        $this->assertEquals(
            new CurrencyPair(new Currency('GBP', 5, 2, '&#163;'), new Currency('EUR', 5, 2, '&#8364;'), 0.9),
            $this->manager->getCurrencyPair('GB', 'FR', 0.9));
    }

    /**
     * @expectedException Lmh\Bundle\MoneyBundle\Exception\InvalidArgumentException
     */
    public function testGetCurrencyPairForCountriesInvalidArgument()
    {
        $test = new CurrencyPair(new Currency('BLAH', 5, 2, '&#36;'), new Currency('FOO', 5, 2, '&#163;'), 1.7);
    }
}
