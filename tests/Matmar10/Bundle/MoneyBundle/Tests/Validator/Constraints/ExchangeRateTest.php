<?php

namespace Matmar10\Bundle\MoneyBundle\Tests\Validator\Constraints;

use Matmar10\Bundle\MoneyBundle\Validator\Constraints\ExchangeRate as ExchangeRateConstraint;
use Matmar10\Money\Entity\Currency;
use Matmar10\Money\Entity\ExchangeRate;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ExchangeRateTest extends WebTestCase
{

    protected $currencyManager;
    protected $validator;

    public function setUp()
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();
        $this->validator = $container->get('validator');
    }

    public function testValidateValue()
    {;
        
        $exchangeRateConstraint = new ExchangeRateConstraint();

        $from = new Currency('BTC', 8, 8);
        $to = new Currency('USD', 2, 2);
        $validExchangeRate = new ExchangeRate($from, $to, 150);

        // use the validator to validate the value
        $violationList = $this->validator->validateValue(
            $validExchangeRate,
            $exchangeRateConstraint
        );

        $this->assertEquals(0, $violationList->count());

        $violationList2 = $this->validator->validateValue(
            null,
            $exchangeRateConstraint
        );
        $this->assertEquals('The value for the property  is not a valid Matmar10\Money\Entity\ExchangeRateInterface instance', $violationList2[0]->getMessage());

    }

}
