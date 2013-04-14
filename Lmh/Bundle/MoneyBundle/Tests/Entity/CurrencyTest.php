<?php

namespace Lmh\Bundle\MoneyBundle\Tests\Entity;

use JMS\Serializer\SerializerBuilder;
use Lmh\Bundle\MoneyBundle\Entity\Currency;
use PHPUnit_Framework_TestCase as TestCase;

class CurrencyTest extends TestCase
{
    public function testEquals()
    {
        $usdCode = 'USD';
        $usd = new Currency($usdCode, 5, 2);
        $usd2 = new Currency($usdCode, 5, 2);
        $usd3 = new Currency($usdCode, 7, 2);

        $this->assertTrue($usd->equals($usd2));
        $this->assertTrue($usd2->equals($usd));
        $this->assertFalse($usd->equals($usd3));

        $eurCode = 'EUR';
        $eur = new Currency($eurCode, 5, 2);
        $eur2 = new Currency($eurCode, 5, 2);
        $eur3 = new Currency($eurCode, 6, 2);

        $this->assertTrue($eur->equals($eur2));
        $this->assertTrue($eur2->equals($eur));
        $this->assertFalse($eur->equals($eur3));

        $this->assertFalse($usd->equals($eur));
        $this->assertFalse($eur->equals($usd));
    }
    
    /**
     * @expectedException Lmh\Bundle\MoneyBundle\Exception\InvalidCurrencyCodeException
     */
    public function testException()
    {
        $invalidCode = 'USDA';
        $usda = new Currency($invalidCode, 5, 2);
    }

    public function testSerialize()
    {
        
        $serializer = SerializerBuilder::create()->build();

        $usd = new Currency('USD', 5, 2);
        $json = $serializer->serialize($usd, 'json');
        $this->assertEquals('{"currencyCode":"USD","precision":5,"displayPrecision":2}', $json);

        $cad = new Currency('CAD', 8, 8);
        $json2 = $serializer->serialize($cad, 'json');
        $this->assertEquals('{"currencyCode":"CAD","precision":8,"displayPrecision":8}', $json2);

    }

    public function testDeserialize()
    {

        $serializer = SerializerBuilder::create()->build();

        $usd = new Currency('USD', 5, 2);
        $json = '{"currencyCode":"USD","precision":5,"displayPrecision":2}';
        $usdResult = $serializer->deserialize($json, 'Lmh\Bundle\MoneyBundle\Entity\Currency', 'json');
        $this->assertEquals($usd, $usdResult);

        $cad = new Currency('CAD', 8, 8);
        $json2 = '{"currencyCode":"CAD","precision":8,"displayPrecision":8}';
        $cadResult = $serializer->deserialize($json2, 'Lmh\Bundle\MoneyBundle\Entity\Currency', 'json');
        $this->assertEquals($cad, $cadResult);

    }
}
