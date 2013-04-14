<?php

namespace Lmh\Bundle\MoneyBundle\Entity;

use JMS\Serializer\Annotation\AccessType;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\ReadOnly;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

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
        if(!$this->getCurrency()->equals($money->getCurrency())) {
            throw new InvalidArgumentException("Cannot add Money of Currency " . $money->getCurrency() .
                                 " to Money of Currency " . $this->getCurrency() . ".");
        }
        $newMoney = new Money($this->getCurrency());
        $newAmountInteger = $this->getAmountInteger() + $money->getAmountInteger();
        $newMoney->setAmountInteger($newAmountInteger);
        return $newMoney;
    }

    public function subtract(Money $money)
    {
        if(!$this->getCurrency()->equals($money->getCurrency())) {
            throw new InvalidArgumentException("Cannot subtract Money of Currency " . $money->getCurrency() .
                                 " to Money of Currency " . $this->getCurrency() . ".");
        }
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

    public function isLess(Money $rightHandValue)
    {
        if(!$this->currency->equals($rightHandValue->getCurrency())) {
            return false;
        }
        return $this->amountInteger < $rightHandValue->getAmountInteger();
    }

    public function isGreater(Money $rightHandValue)
    {
        if(!$this->currency->equals($rightHandValue->getCurrency())) {
            return false;
        }
        return $this->amountInteger > $rightHandValue->getAmountInteger();
    }

    public function isEqual(Money $rightHandValue)
    {
        if(!$this->currency->equals($rightHandValue->getCurrency())) {
            return false;
        }
        return $this->amountInteger === $rightHandValue->getAmountInteger();
    }

    public function isLessOrEqual(Money $rightHandValue)
    {
        if(!$this->currency->equals($rightHandValue->getCurrency())) {
            return false;
        }
        return $this->amountInteger <= $rightHandValue->getAmountInteger();
    }

    public function isGreaterOrEqual(Money $rightHandValue)
    {
        if(!$this->currency->equals($rightHandValue->getCurrency())) {
            return false;
        }
        return $this->amountInteger >= $rightHandValue->getAmountInteger();
    }
}
