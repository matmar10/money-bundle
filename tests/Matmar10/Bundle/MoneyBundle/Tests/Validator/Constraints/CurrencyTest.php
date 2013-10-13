<?php

namespace Matmar10\Bundle\MoneyBundle\Tests\Validator\Constraints;

use Matmar10\Bundle\MoneyBundle\Validator\Constraints\Currency as AssertCurrency;
use Matmar10\Money\Entity\Currency;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CurrencyTest extends WebTestCase
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
        
        $currencyConstraint = new AssertCurrency();

        $validCurrency = new Currency('BTC', 8, 8);

        // use the validator to validate the value
        $violationList = $this->validator->validateValue(
            $validCurrency,
            $currencyConstraint
        );

        $this->assertEquals(0, $violationList->count());

        $violationList2 = $this->validator->validateValue(
            null,
            $currencyConstraint
        );
        $this->assertEquals('The value for the property  is not a valid Matmar10\Money\Entity\CurrencyInterface instance', $violationList2[0]->getMessage());

    }

}
