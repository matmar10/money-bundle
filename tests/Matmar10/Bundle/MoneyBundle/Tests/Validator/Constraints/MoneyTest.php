<?php

namespace Matmar10\Bundle\MoneyBundle\Tests\Validator\Constraints;

use Matmar10\Bundle\MoneyBundle\Validator\Constraints\Money as AssertMoney;
use Matmar10\Money\Entity\Currency;
use Matmar10\Money\Entity\Money;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MoneyTest extends WebTestCase
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
        
        $moneyConstraint = new AssertMoney();

        $currency = new Currency('BTC', 8, 8);
        $validMoney = new Money($currency);

        // use the validator to validate the value
        $violationList = $this->validator->validateValue(
            $validMoney,
            $moneyConstraint
        );

        $this->assertEquals(0, $violationList->count());

        $violationList2 = $this->validator->validateValue(
            null,
            $moneyConstraint
        );
        $this->assertEquals('The value for the property  is not a valid Matmar10\Money\Entity\MoneyInterface instance', $violationList2[0]->getMessage());

    }

}
