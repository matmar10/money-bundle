<?php

namespace Lmh\Bundle\MoneyBundle\Entity;

use JMS\Serializer\Annotation\AccessType;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\ReadOnly;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use Lmh\Bundle\MoneyBundle\Exception\InvalidArgumentException;

/**
 * @AccessType("public_method")
 * @ExclusionPolicy("none")
 */
class Money
{

    const ROUND_TO_DISPLAY = 'ROUND_TO_DISPLAY';
    const ROUND_TO_DEFAULT = 'ROUND_TO_DEFAULT';

    /**
     * @Type("Lmh\Bundle\MoneyBundle\Entity\Currency")
     */
    protected $currency;

    /**
     * @Type("integer")
     * @ReadOnly
     */
    protected $scale;

    /**
     * @Type("integer")
     * @SerializedName("amountInteger")
     */
    protected $amountInteger = 0;

    /**
     * @Type("double")
     * @SerializedName("amountFloat")
     */
    protected $amountFloat;

    /**
     * @Type("string")
     * @SerializedName("amountDisplay")
     */
    protected $amountDisplay;

    public function __construct(Currency $currency) {
        $this->setCurrency($currency);
    }

    public function setCurrency(Currency $currency)
    {
        $this->currency = $currency;
        $this->scale = bcpow(10, $currency->getPrecision(), 0);
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getScale()
    {
        return $this->scale;
    }

    public function setAmountFloat($amountFloat)
    {
        $this->amountInteger = bcmul($amountFloat, $this->scale, 0);
    }

    public function getAmountFloat($roundTo = self::ROUND_TO_DEFAULT)
    {
        $scaled = bcdiv($this->amountInteger, $this->scale, $this->currency->getPrecision());

        if(self::ROUND_TO_DEFAULT === $roundTo) {
            $rounding = $this->currency->getPrecision();
        } else if(self::ROUND_TO_DISPLAY === $roundTo) {
            $rounding = $this->currency->getDisplayPrecision();
        } else {
            $rounding = $this->currency->getPrecision() + $roundTo;
        }

        return round($scaled, $rounding);
    }

    public function setAmountInteger($amountInteger)
    {
        $this->amountInteger = $amountInteger;
    }

    public function getAmountInteger()
    {
        return $this->amountInteger;
    }

    public function getAmountDisplay()
    {
        $decimals = $this->getCurrency()->getDisplayPrecision();
        $formatter = "%01.{$decimals}f";
        return sprintf($formatter, $this->getAmountFloat());
    }

    public function setAmountDisplay($amountDisplay)
    {
        $this->setAmountFloat((float)$amountDisplay);
    }

    public function __toString()
    {
        return $this->getAmountDisplay();
    }

    public function add(Money $money)
    {
        $this->assertSameCurrency($money);
        $newMoney = new Money($this->getCurrency());
        $newAmountInteger = $this->getAmountInteger() + $money->getAmountInteger();
        $newMoney->setAmountInteger($newAmountInteger);
        return $newMoney;
    }

    public function subtract(Money $money)
    {
        $this->assertSameCurrency($money);
        $newMoney = new Money($this->getCurrency());
        $newAmountInteger = $this->getAmountInteger() - $money->getAmountInteger();
        $newMoney->setAmountInteger($newAmountInteger);
        return $newMoney;
    }

    public function multiply($multiplier)
    {
        $newMoney = new Money($this->getCurrency());
        $newAmountInteger = bcmul($this->getAmountInteger(), $multiplier, 0);
        $newMoney->setAmountInteger($newAmountInteger);
        return $newMoney;
    }

    public function divide($divisor)
    {
        $newMoney = new Money($this->getCurrency());
        $newAmountInteger = bcdiv($this->getAmountInteger(), $divisor, 0);
        $newMoney->setAmountInteger($newAmountInteger);
        return $newMoney;
    }

    public function isSameCurrency(Money $rightHandValue)
    {
        return $this->currency->equals($rightHandValue->getCurrency());
    }

    public function assertSameCurrency(Money $rightHandValue)
    {
        if(!$this->isSameCurrency($rightHandValue)) {
            throw new InvalidArgumentException("Different currencies provided: Money object of Currency type '" .
                        $this->currency->getCurrencyCode() . "' expected.");
        }
    }

    public function isLess(Money $rightHandValue)
    {
        $this->assertSameCurrency($rightHandValue);
        return $this->amountInteger < $rightHandValue->getAmountInteger();
    }

    public function isGreater(Money $rightHandValue)
    {
        $this->assertSameCurrency($rightHandValue);
        return $this->amountInteger > $rightHandValue->getAmountInteger();
    }

    public function isEqual(Money $rightHandValue)
    {
        $this->assertSameCurrency($rightHandValue);
        return $this->amountInteger === $rightHandValue->getAmountInteger();
    }

    public function isLessOrEqual(Money $rightHandValue)
    {
        $this->assertSameCurrency($rightHandValue);
        return $this->amountInteger <= $rightHandValue->getAmountInteger();
    }

    public function isGreaterOrEqual(Money $rightHandValue)
    {
        $this->assertSameCurrency($rightHandValue);
        return $this->amountInteger >= $rightHandValue->getAmountInteger();
    }

    public function compare(Money $rightHandValue)
    {
        $this->assertSameCurrency($rightHandValue);
        $otherAmount = $rightHandValue->getAmountInteger();

        if($this->amountInteger < $otherAmount) {
            return -1;
        }

        if($this->amountInteger === $otherAmount) {
            return 0;
        }
        // $this->amountInteger > $otherAmount
        return 1;
    }

    public function isZero()
    {
        return $this->amountInteger === 0;
    }
    
    public function isPositive()
    {
        return $this->amountInteger > 0;
    }

    public function isNegative()
    {
        return $this->amountInteger < 0;
    }

    public function allocate(array $ratios, $roundToPrecision = self::ROUND_TO_DEFAULT)
    {
        $total = array_sum($ratios);
        if(!count($ratios) || !$total) {
            throw new InvalidArgumentException('Invalid ratios specified: at least one ore more positive ratios must be provided.');
        }
        $remainder = clone $this;
        $results = array();
        if(self::ROUND_TO_DISPLAY === $roundToPrecision) {
            $increment = $this->scale / pow(10, $this->currency->getDisplayPrecision());
        } elseif(self::ROUND_TO_DEFAULT === $roundToPrecision) {
            $increment = $this->scale / pow(10, $this->currency->getPrecision());
        } else {
            throw new InvalidArgumentException("Invalid roundToPrecision argument '" . $roundToPrecision . "' specified (valid options are: '" . self::ROUND_TO_DISPLAY . "', '" . self::ROUND_TO_DEFAULT . "').");
        }

        foreach ($ratios as $ratio) {
            if($ratio < 0) {
                throw new InvalidArgumentException("Invalid share ratio '" . $ratio . "' supplied: ratios may not be negative amounts.");
            }
            $share = $this->multiply($ratio)->divide($total);
            $results[] = $share;
            $remainder = $remainder->subtract($share);
        }
        
        for ($i = 0; $remainder->isPositive(); $i++) {
            $amountInteger = $results[$i]->getAmountInteger();
            $results[$i]->setAmountInteger($amountInteger + $increment);
            $increment = $this->scale / pow(10, $this->currency->getPrecision());
            $remainderAmountInteger = $remainder->getAmountInteger();
            $remainder->setAmountInteger($remainderAmountInteger - $increment);
        }

        foreach($results as &$result) {
            if(self::ROUND_TO_DISPLAY === $roundToPrecision) {
                $result->setAmountFloat($result->getAmountDisplay());
                continue;
            }
        }

        return $results;
    }
    
}
