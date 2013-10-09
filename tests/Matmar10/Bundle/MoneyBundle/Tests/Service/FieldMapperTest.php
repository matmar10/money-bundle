<?php

namespace Matmar10\Bundle\MoneyBundle\Tests\Service;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Lmh\Bundle\MoneyBundle\Tests\Fixtures\Entity\AnnotatedTestEntity;
use Matmar10\Money\Entity\CurrencyPair;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FieldMapperTest extends WebTestCase
{
    /**
     * @var \Matmar10\Bundle\MoneyBundle\Service\CurrencyManager
     */
    protected $currencyManager;

    /**
     * @var \Matmar10\Bundle\MoneyBundle\Service\FieldMapper
     */
    protected $fieldMapper;

    public function setUp()
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $this->currencyManager = $kernel->getContainer()->get('matmar10_money.currency_manager');
        $this->fieldMapper = $kernel->getContainer()->get('matmar10_money.field_mapper');
        AnnotationRegistry::registerAutoloadNamespace('Lmh\\Bundle\\MoneyBundle\\Annotation', __DIR__.'/../../../src/');
    }

    public function testPrePersist()
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
        $multiplier = 1.18;
        $currencyPair = new CurrencyPair($gbp, $eur, $multiplier);
        $entity->setExampleCurrencyPair($currencyPair);

        // process the field mappings
        $this->fieldMapper->prePersist($entity);

        // test Currency
        $this->assertEquals('BTC', $entity->getExampleCurrencyCode());
        // test Money
        $this->assertEquals(199, $entity->getExampleMoneyAmountInteger());
        $this->assertEquals('USD', $entity->getExampleMoneyCurrencyCode());
        // test CurrencyPair
        $this->assertEquals('GBP', $entity->getExampleCurrencyPairFromCurrencyCode());
        $this->assertEquals('EUR', $entity->getExampleCurrencyPairToCurrencyCode());
        $this->assertEquals($multiplier, $entity->getExampleCurrencyPairMultiplier());
    }

    public function testPostPersist()
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
        $entity->setExampleCurrencyPairMultiplier(1.18);

        // process the field mappings
        $this->fieldMapper->postPersist($entity);

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
        $multiplier = 1.18;
        $currencyPair = new CurrencyPair($gbp, $eur, $multiplier);
        $entity->setExampleCurrencyPair($currencyPair);
        $this->assertEquals($currencyPair, $entity->getExampleCurrencyPair());
    }

}
