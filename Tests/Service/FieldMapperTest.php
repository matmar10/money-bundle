<?php

namespace Lmh\Bundle\MoneyBundle\Tests\Service;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Lmh\Bundle\MoneyBundle\Tests\Fixtures\Entity\AnnotatedTestEntity;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FieldMapperTest extends WebTestCase
{
    /**
     * @var \Lmh\Bundle\MoneyBundle\Service\CurrencyManager
     */
    protected $currencyManager;

    /**
     * @var \Lmh\Bundle\MoneyBundle\Service\FieldMapper
     */
    protected $fieldMapper;

    public function setUp()
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $this->currencyManager = $kernel->getContainer()->get('lmh_money.currency_manager');
        $this->fieldMapper = $kernel->getContainer()->get('lmh_money.field_mapper');
        AnnotationRegistry::registerAutoloadNamespace('Lmh\\Bundle\\MoneyBundle\\Annotation', __DIR__.'/../../../../../');
    }

    public function testPrePersist()
    {
        $entity = new AnnotatedTestEntity();
        $btc = $this->currencyManager->getCurrency('BTC');
        $entity->setExampleCurrency($btc);
        $usdAmount = $this->currencyManager->getMoney('USD');
        $usdAmount->setAmountFloat(1.99);
        $entity->setExampleMoney($usdAmount);
        $this->fieldMapper->prePersist($entity);
        $this->assertEquals('BTC', $entity->getExampleCurrencyCode());
        $this->assertEquals(199, $entity->getExampleMoneyAmountInteger());
        $this->assertEquals('USD', $entity->getExampleMoneyCurrencyCode());
    }

    public function testPostPersist()
    {
        $entity = new AnnotatedTestEntity();
        $entity->setExampleCurrencyCode('BTC');
        $entity->setExampleMoneyAmountInteger(321);
        $entity->setExampleMoneyCurrencyCode('USD');

        $this->fieldMapper->postPersist($entity);

        $btc = $this->currencyManager->getCurrency('BTC');
        $usdAmount = $this->currencyManager->getMoney('USD');
        $usdAmount->setAmountFloat(3.21);

        $this->assertEquals($btc, $entity->getExampleCurrency());
        $this->assertEquals($usdAmount, $entity->getExampleMoney());
    }

}
