<?php

namespace Matmar10\Bundle\MoneyBundle\Tests\Validator\Constraints;

use Matmar10\Bundle\MoneyBundle\Validator\Constraints\CurrencyPair as AssertCurrencyPair;
use Matmar10\Money\Entity\CurrencyPair;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CurrencyPairTest extends WebTestCase
{

    protected $currencyManager;
    protected $validator;

    public function setUp()
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        $this->currencyManager = $container->get('matmar10_money.currency_manager');
        $this->validator = $container->get('validator');
    }

    public function testValidateValue()
    {
        
        $currencyPairConstraint = new AssertCurrencyPair();

        $from = $this->currencyManager->getCurrency('BTC');
        $to = $this->currencyManager->getCurrency('USD');
        $validPair = new CurrencyPair($from, $to);

        // use the validator to validate the value
        $violationList = $this->validator->validateValue(
            $validPair,
            $currencyPairConstraint
        );

        $this->assertEquals(0, $violationList->count());

        $violationList2 = $this->validator->validateValue(
            null,
            $currencyPairConstraint
        );
        $this->assertEquals('The value for the property  is not a valid Matmar10\Money\Entity\CurrencyPairInterface instance', $violationList2[0]->getMessage());

    }

}
