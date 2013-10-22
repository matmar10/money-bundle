<?php

namespace Matmar10\Bundle\MoneyBundle\Tests\Service;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Matmar10\Bundle\MoneyBundle\Tests\Fixtures\Entity\AnnotatedTestEntity;
use Matmar10\Bundle\MoneyBundle\Tests\Fixtures\Entity\ImproperlyCurrencyAnnotatedTestEntity;
use Matmar10\Bundle\MoneyBundle\Tests\Fixtures\Entity\NullCurrencyAnnotatedTestEntity;
use Matmar10\Money\Entity\CurrencyPair;
use Matmar10\Money\Entity\ExchangeRate;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CompositePropertyServiceTest extends WebTestCase
{
    /**
     * @var \Matmar10\Bundle\MoneyBundle\Service\CurrencyManager
     */
    protected $currencyManager;

    /**
     * @var \Matmar10\Bundle\MoneyBundle\Service\CompositePropertyService
     */
    protected $compositePropertyService;

    public function setUp()
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $this->currencyManager = $kernel->getContainer()->get('matmar10_money.currency_manager');
        $this->compositePropertyService = $kernel->getContainer()->get('matmar10_money.composite_property_service');
        AnnotationRegistry::registerAutoloadNamespace('Matmar10\\Bundle\\MoneyBundle\\Annotation', __DIR__.'/../../../src/');
    }

    public function testFlattenCompositeProperties()
    {
        $entity = new AnnotatedTestEntity();

        // set Currency
        $btc = $this->currencyManager->getCurrency('BTC');
        $entity->setExampleCurrency($btc);

        // set Money
        $usdAmount = $this->currencyManager->getMoney('USD');
        $usdAmount->setAmountFloat(1.99);
        $entity->setExampleMoney($usdAmount);

        // set CurrencyPair
        $gbp = $this->currencyManager->getCurrency('GBP');
        $eur = $this->currencyManager->getCurrency('EUR');
        $currencyPair = new CurrencyPair($gbp, $eur);
        $entity->setExampleCurrencyPair($currencyPair);

        // set ExchangeRate
        $mad = $this->currencyManager->getCurrency('MAD');
        $jpy = $this->currencyManager->getCurrency('JPY');
        $multiplier = 11.75;
        $exchangeRate = new ExchangeRate($mad, $jpy, $multiplier);
        $entity->setExampleExchangeRate($exchangeRate);

        // process the field mappings
        $this->compositePropertyService->flattenCompositeProperties($entity);

        // test Currency
        $this->assertEquals('BTC', $entity->getExampleCurrencyCode());

        // test Money
        $this->assertEquals(199, $entity->getExampleMoneyAmountInteger());
        $this->assertEquals('USD', $entity->getExampleMoneyCurrencyCode());

        // test CurrencyPair
        $this->assertEquals('GBP', $entity->getExampleCurrencyPairFromCurrencyCode());
        $this->assertEquals('EUR', $entity->getExampleCurrencyPairToCurrencyCode());

        // test ExchangeRate
        $this->assertEquals('MAD', $entity->getExampleExchangeRateFromCurrencyCode());
        $this->assertEquals('JPY', $entity->getExampleExchangeRateToCurrencyCode());
        $this->assertEquals($multiplier, $entity->getExampleExchangeRateMultiplier());

        // test nullable ExchangeRate
        $this->assertEquals(null, $entity->getExampleNullableExchangeRateFromCurrencyCode());
        $this->assertEquals(null, $entity->getExampleNullableExchangeRateToCurrencyCode());
        $this->assertEquals(null, $entity->getExampleNullableExchangeRateMultiplier());

    }

    public function testComposeCompositeProperties()
    {
        $entity = new AnnotatedTestEntity();

        // set Currency related fields
        $entity->setExampleCurrencyCode('BTC');

        // set Money related fields
        $entity->setExampleMoneyAmountInteger(321);
        $entity->setExampleMoneyCurrencyCode('USD');

        // set CurrencyPair related fields
        $entity->setExampleCurrencyPairFromCurrencyCode('GBP');
        $entity->setExampleCurrencyPairToCurrencyCode('EUR');

        // set ExchangeRate related fields
        $entity->setExampleExchangeRateFromCurrencyCode('MAD');
        $entity->setExampleExchangeRateToCurrencyCode('JPY');
        $entity->setExampleExchangeRateMultiplier(11.75);

        // process the field mappings
        $this->compositePropertyService->composeCompositeProperties($entity);

        // test Currency
        $btc = $this->currencyManager->getCurrency('BTC');
        $this->assertEquals($btc, $entity->getExampleCurrency());

        // test Money
        $usdAmount = $this->currencyManager->getMoney('USD');
        $usdAmount->setAmountFloat(3.21);
        $this->assertEquals($usdAmount, $entity->getExampleMoney());

        // test CurrencyPair
        $gbp = $this->currencyManager->getCurrency('GBP');
        $eur = $this->currencyManager->getCurrency('EUR');
        $currencyPair = new CurrencyPair($gbp, $eur);
        $entity->setExampleCurrencyPair($currencyPair);
        $this->assertEquals($currencyPair, $entity->getExampleCurrencyPair());

        // test ExchangeRate
        $mad = $this->currencyManager->getCurrency('MAD');
        $jpy = $this->currencyManager->getCurrency('JPY');
        $exchangeRate = new ExchangeRate($mad, $jpy, 11.75);
        $entity->setExampleExchangeRate($exchangeRate);
        $this->assertEquals($exchangeRate, $entity->getExampleExchangeRate());
    }

    /**
     * @expectedException Doctrine\Common\Annotations\AnnotationException
     */
    public function testImproperCurrency()
    {
        $entity = new ImproperlyCurrencyAnnotatedTestEntity();

        // process the field mappings
        $this->compositePropertyService->flattenCompositeProperties($entity);
    }

    /**
     * @expectedException Matmar10\Bundle\MoneyBundle\Exception\NullPropertyMappingException
     */
    public function testNullFieldMappingException()
    {
        $entity = new NullCurrencyAnnotatedTestEntity();

        // process the field mappings
        $this->compositePropertyService->flattenCompositeProperties($entity);
    }
}
