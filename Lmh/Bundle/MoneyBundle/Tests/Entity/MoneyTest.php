<?php

namespace Lmh\Bundle\MoneyBundle\Tests\Entity;

use JMS\Serializer\SerializerBuilder;
use Lmh\Bundle\MoneyBundle\Entity\Currency;
use Lmh\Bundle\MoneyBundle\Entity\Money;
use PHPUnit_Framework_TestCase as TestCase;

class MoneyTest extends TestCase
{
    protected $usdCode;
    protected $usd;
    protected $usdMoney;
    protected $eurCode;
    protected $eur;
    protected $eurMoney;

    public function setUp()
    {
        $this->usdCode = 'USD';
        $this->usd = new Currency($this->usdCode, 10, 2);
        $this->usdMoney = new Money($this->usd);

        $this->eurCode = 'EUR';
        $this->eur = new Currency($this->eurCode, 10, 2);
        $this->eurMoney = new Money($this->eur);
    }

    public function testAmountFloat()
    {
        $usd = clone $this->usdMoney;
        $usd->setAmountFloat(1.23);
        $this->assertEquals(1.23, $usd->getAmountFloat());

        $usdCurrency = clone $this->usd;
        $usdCurrency->setPrecision(10);
        $usdCurrency->setDisplayPrecision(10);

        $usd2 = clone $this->usdMoney;
        $usd2->setCurrency($usdCurrency);

        $usd2->setAmountFloat(0.123456789);
        $this->assertEquals(0.123456789, $usd2->getAmountFloat());
    }

    public function testAmountInteger()
    {

        $usdCurrency = clone $this->usd;
        $usdCurrency->setPrecision(10);
        $usdCurrency->setDisplayPrecision(10);

        $usd = clone $this->usdMoney;
        $usd->setCurrency($usdCurrency);
        $usd->setAmountInteger(123);
        $this->assertEquals(123, $usd->getAmountInteger());
        $this->assertEquals(0.0000000123, $usd->getAmountFloat());

        $usd2 = clone $this->usdMoney;
        $usd2->setCurrency($usdCurrency);
        $usd2->setAmountInteger(1234567899);
        $this->assertEquals(1234567899, $usd2->getAmountInteger());
        $this->assertEquals(0.1234567899, $usd2->getAmountFloat());
    }

    public function testAmountDisplay()
    {

        $usdCurrency = clone $this->usd;
        $usdCurrency->setPrecision(5);
        $usdCurrency->setDisplayPrecision(2);

        $usd = clone $this->usdMoney;
        $usd->setCurrency($usdCurrency);

        $usd->setAmountInteger(123456);
        $this->assertEquals(123456, $usd->getAmountInteger());
        $this->assertEquals(1.23456, $usd->getAmountFloat());
        $this->assertEquals("1.23", $usd->getAmountDisplay());

        $usd->setAmountDisplay("9.87");
        $this->assertEquals(987000, $usd->getAmountInteger());
        $this->assertEquals(9.87, $usd->getAmountFloat());
        $this->assertEquals("9.87", $usd->getAmountDisplay());

        $usd->setAmountDisplay("1.23456");
        $this->assertEquals(123456, $usd->getAmountInteger());
        $this->assertEquals(1.23456, $usd->getAmountFloat());
        $this->assertEquals("1.23", $usd->getAmountDisplay());

        $usd->setAmountDisplay("1.55555");
        $this->assertEquals(155555, $usd->getAmountInteger());
        $this->assertEquals(1.55555, $usd->getAmountFloat());
        $this->assertEquals("1.56", $usd->getAmountDisplay());
    }

    public function testAdd()
    {

        $usdCurrency = clone $this->usd;
        $usdCurrency->setPrecision(5);
        $usdCurrency->setDisplayPrecision(2);

        $usd1 = clone $this->usdMoney;
        $usd1->setCurrency($usdCurrency);
        $usd1->setAmountInteger(999000); // 9.99

        $usd2 = clone $this->usdMoney;
        $usd2->setCurrency($usdCurrency);
        $usd2->setAmountInteger(1000000); // 10.00

        $sum = $usd1->add($usd2);
        $this->assertEquals(1999000, $sum->getAmountInteger());
        $this->assertEquals(19.99, $sum->getamountFloat());
        $this->assertEquals('19.99', $sum->getAmountDisplay());
        
        $usdCurrency2 = clone $this->usd;
        $usdCurrency2->setPrecision(5);
        $usdCurrency2->setDisplayPrecision(5);
        $usd1->setCurrency($usdCurrency2);
        $usd2->setCurrency($usdCurrency2);

        $usd1->setAmountInteger(999800); // 9.998
        $usd2->setAmountInteger(1000100); // 10.001
        $sum2 = $usd1->add($usd2);
        $this->assertEquals(1999900, $sum2->getAmountInteger());
        $this->assertEquals(19.999, $sum2->getamountFloat());
        $this->assertEquals('19.999', $sum2->getAmountDisplay());
    }

    public function testSubtract()
    {

        $usdCurrency = clone $this->usd;
        $usdCurrency->setPrecision(5);
        $usdCurrency->setDisplayPrecision(2);

        $usd1 = clone $this->usdMoney;
        $usd1->setCurrency($usdCurrency);
        $usd1->setAmountInteger(999000); // 9.99

        $usd2 = clone $this->usdMoney;
        $usd2->setCurrency($usdCurrency);
        $usd2->setAmountInteger(1000000); // 10.00

        $difference = $usd1->subtract($usd2);
        $this->assertEquals(-1000, $difference->getAmountInteger());
        $this->assertEquals(-0.01, $difference->getamountFloat());
        $this->assertEquals('-0.01', $difference->getAmountDisplay());

        $difference2 = $usd2->subtract($usd1);
        $this->assertEquals(1000, $difference2->getAmountInteger());
        $this->assertEquals(0.01, $difference2->getamountFloat());
        $this->assertEquals('0.01', $difference2->getAmountDisplay());
        
        $usdCurrency2 = clone $this->usd;
        $usdCurrency2->setPrecision(5);
        $usdCurrency2->setDisplayPrecision(5);
        $usd1->setCurrency($usdCurrency2);
        $usd2->setCurrency($usdCurrency2);

        $usd1->setAmountInteger(12346);  // 0.12346
        $usd2->setAmountInteger(1); // 0.00001
        $difference3 = $usd1->subtract($usd2);
        $this->assertEquals(12345, $difference3->getAmountInteger());
        $this->assertEquals(0.12345, $difference3->getamountFloat());
        $this->assertEquals('0.12345', $difference3->getAmountDisplay());
    }

    public function testMultiply()
    {

        $usdCurrency = clone $this->usd;
        $usdCurrency->setPrecision(5);
        $usdCurrency->setDisplayPrecision(2);

        $usd1 = clone $this->usdMoney;
        $usd1->setCurrency($usdCurrency);

        $usd1->setAmountInteger(100000); // 1.00
        $product1 = $usd1->multiply(10);
        $this->assertEquals(10, $product1->getAmountFloat());

        $usd1->setAmountInteger(150000); // 1.00        
        $product1 = $usd1->multiply(2);
        $this->assertEquals(3, $product1->getAmountFloat());


        $usdCurrency2 = clone $this->usd;
        $usdCurrency2->setPrecision(10);
        $usdCurrency2->setDisplayPrecision(10);
        $usd1->setCurrency($usdCurrency2);
        $usd1->setAmountInteger(12345600000); // 1.23456
        $product1 = $usd1->multiply(6.54321);
        $this->assertEquals('8.0779853376', $product1->getAmountDisplay());
        // note that using a float literal won't work for this test

    }

    public function testDivide()
    {

        $usdCurrency = clone $this->usd;
        $usdCurrency->setPrecision(5);
        $usdCurrency->setDisplayPrecision(2);

        $usd1 = clone $this->usdMoney;
        $usd1->setCurrency($usdCurrency);

        $usd1->setAmountInteger(100000); // 1.00
        $quotient1 = $usd1->divide(0.01);
        $this->assertEquals(100, $quotient1->getAmountFloat());

        $usdCurrency2 = clone $this->usd;
        $usdCurrency2->setPrecision(11);
        $usdCurrency2->setDisplayPrecision(11);
        $usd1->setCurrency($usdCurrency2);

        $usd1->setAmountInteger(123456000000); // 1.23456
        $quotient2 = $usd1->divide(3.21000000000);
        $this->assertEquals('0.38459813084', $quotient2->getAmountFloat());
    }

    public function testIsLess()
    {

        $usdCurrency = clone $this->usd;
        $usdCurrency->setPrecision(20);

        $usd1 = clone $this->usdMoney;
        $usd1->setCurrency($usdCurrency);
        $usd2 = clone $this->usdMoney;
        $usd2->setCurrency($usdCurrency);

        $usd1->setAmountInteger('1000000000000000001');
        $usd2->setAmountInteger('10000000000000000001');
        $this->assertTrue($usd1->isLess($usd2));
        
        $usd1->setAmountFloat(9);
        $usd2->setAmountFloat(10);
        $this->assertTrue($usd1->isLess($usd2));
    }

    public function testIsLessOrEqual()
    {

        $usdCurrency = clone $this->usd;
        $usdCurrency->setPrecision(20);

        $usd1 = clone $this->usdMoney;
        $usd1->setCurrency($usdCurrency);
        $usd2 = clone $this->usdMoney;
        $usd2->setCurrency($usdCurrency);

        $usd1->setAmountInteger('1000000000000000001');
        $usd2->setAmountInteger('10000000000000000001');
        $this->assertTrue($usd1->isLessOrEqual($usd2));
        $usd2->setAmountInteger('1000000000000000001');
        $this->assertTrue($usd1->isLessOrEqual($usd2));

        $usd1->setAmountFloat(9);
        $usd2->setAmountFloat(10);
        $this->assertTrue($usd1->isLessOrEqual($usd2));
        $usd2->setAmountFloat(9);
        $this->assertTrue($usd1->isLessOrEqual($usd2));
    }

    public function testIsGreater()
    {

        $usdCurrency = clone $this->usd;
        $usdCurrency->setPrecision(20);

        $usd1 = clone $this->usdMoney;
        $usd1->setCurrency($usdCurrency);
        $usd2 = clone $this->usdMoney;
        $usd2->setCurrency($usdCurrency);

        $usd1->setAmountInteger('1000000000000000001');
        $usd2->setAmountInteger('10000000000000000001');
        $this->assertTrue($usd2->isGreater($usd1));

        $usd1->setAmountFloat(9);
        $usd2->setAmountFloat(10);
        $this->assertTrue($usd2->isGreater($usd1));
    }

    public function testIsGreaterOrEqual()
    {

        $usdCurrency = clone $this->usd;
        $usdCurrency->setPrecision(20);

        $usd1 = clone $this->usdMoney;
        $usd1->setCurrency($usdCurrency);
        $usd2 = clone $this->usdMoney;
        $usd2->setCurrency($usdCurrency);

        $usd1->setAmountInteger('1000000000000000001');
        $usd2->setAmountInteger('10000000000000000001');
        $this->assertTrue($usd2->isGreaterOrEqual($usd1));
        $usd2->setAmountInteger('1000000000000000001');
        $this->assertTrue($usd2->isGreaterOrEqual($usd1));

        $usd1->setAmountFloat(9);
        $usd2->setAmountFloat(10);
        $this->assertTrue($usd2->isGreaterOrEqual($usd1));
        $usd2->setAmountFloat(9);
        $this->assertTrue($usd2->isGreaterOrEqual($usd1));
    }

    public function testSerialize()
    {

        $serializer = SerializerBuilder::create()->build();

        $usd = new Currency('USD', 5, 2);
        $usdMoney = new Money($usd);
        $usdMoney->setAmountFloat(1.23);
        $json = $serializer->serialize($usdMoney, 'json');
        $this->assertEquals('{"currency":{"currencyCode":"USD","precision":5,"displayPrecision":2},"scale":100000,"amountInteger":123000,"amountFloat":1.23,"amountDisplay":"1.23"}', $json);

        $cad = new Currency('CAD', 8, 8);
        $cadMoney = new Money($cad);
        $json2 = $serializer->serialize($cadMoney, 'json');
        $this->assertEquals('{"currency":{"currencyCode":"CAD","precision":8,"displayPrecision":8},"scale":100000000,"amountInteger":0,"amountFloat":0,"amountDisplay":"0.00000000"}', $json2);

    }

    public function testDeserialize()
    {

        $serializer = SerializerBuilder::create()->build();

        $usd = new Currency('USD', 5, 2);
        $usdMoney = new Money($usd);
        $usdMoney->setAmountFloat(1.23);
        $json = '{"currency":{"currencyCode":"USD","precision":5,"displayPrecision":2},"amountInteger":123000,"amountFloat":1.23,"amountDisplay":"1.23"}';
        $usdResult = $serializer->deserialize($json, 'Lmh\Bundle\MoneyBundle\Entity\Money', 'json');
        $this->assertEquals($usdMoney, $usdResult);

        $cad = new Currency('CAD', 8, 8);
        $cadMoney = new Money($cad);
        $json = '{"currency":{"currencyCode":"CAD","precision":8,"displayPrecision":8},"amountInteger":0,"amountFloat":0,"amountDisplay":"0.00000000"}';
        $cadResult = $serializer->deserialize($json, 'Lmh\Bundle\MoneyBundle\Entity\Money', 'json');
        $this->assertEquals($cadMoney, $cadResult);
    }
}