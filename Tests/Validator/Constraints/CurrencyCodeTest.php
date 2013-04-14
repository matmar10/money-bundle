<?php

namespace Lmh\Bundle\MoneyBundle\Tests\Validator\Constraints;

use Lmh\Bundle\MoneyBundle\Entity\Currency;
use Lmh\Bundle\MoneyBundle\Validator\Constraints\CurrencyCode;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CurrencyCodeTest extends WebTestCase
{

    private function getKernel()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        return $kernel;
    }

    public function testValidateValue()
    {
        $kernel = $this->getKernel();
        $validator = $kernel->getContainer()->get('validator');
        
        $currencyCodeConstraint = new CurrencyCode();

        // use the validator to validate the value
        $violationList = $validator->validateValue(
            'USDA',
            $currencyCodeConstraint
        );


        $this->assertEquals(2, $violationList->count());
        $this->assertEquals('The currency code "USDA" is invalid: currency code must be three (3) characters.', $violationList[0]->getMessage());
        $this->assertEquals('The currency code "USDA" is invalid: that currency code is not supported.', $violationList[1]->getMessage());

        // use the validator to validate the value
        $violationList2 = $validator->validateValue(
            'USD',
            $currencyCodeConstraint
        );

        // $this->assertEquals(0, $violationList2->count());

        foreach($violationList2 as $violation) {
            print_r($violation->getMessage());
        }

        $violationList3 = $validator->validateValue(
            'BTC',
            $currencyCodeConstraint
        );
        $this->assertEquals(0, $violationList3->count());
    }

}
