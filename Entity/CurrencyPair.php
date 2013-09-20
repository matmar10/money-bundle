<?php

namespace Lmh\Bundle\MoneyBundle\Entity;

use JMS\Serializer\Annotation\AccessType;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\ReadOnly;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use Lmh\Bundle\MoneyBundle\Entity\BaseCurrencyPair;
use Lmh\Bundle\MoneyBundle\Entity\CurrencyInterface;
use Lmh\Bundle\MoneyBundle\Entity\Money;
use Lmh\Bundle\MoneyBundle\Exception\InvalidArgumentException;

/**
 * @AccessType("public_method")
 * @ExclusionPolicy("none")
 */
class CurrencyPair extends BaseCurrencyPair
{
    /**
     * @Type("double")
     */
    protected $multiplier;

    public function __construct(CurrencyInterface $fromCurrency, CurrencyInterface $toCurrency, $multiplier) {
        parent::__construct($fromCurrency, $toCurrency);
        $this->multiplier = $multiplier;
    }

    public function setMultiplier($multiplier)
    {
        $this->multiplier = $multiplier;
    }

    public function getMultiplier()
    {
        return $this->multiplier;
    }
    
    public function convert(Money $amount)
    {

        if($amount->getCurrency()->equals($this->getFromCurrency())) {
            $newAmount = $amount->multiply($this->getMultiplier());
            $newMoney = new Money($this->toCurrency);
            $newMoney->setAmountFloat($newAmount->getAmountFloat());
            return $newMoney;
        }

        if($amount->getCurrency()->equals($this->getToCurrency())) {
            $newAmount = $amount->divide($this->getMultiplier());
            $newMoney = new Money($this->fromCurrency);
            $newMoney->setAmountFloat($newAmount->getAmountFloat());
            return $newMoney;
        }

        throw new InvalidArgumentException("Cannot convert from " . $amount->getCurrency()->getCurrencyCode() .
            " using CurrencyRate of " .
            $this->getFromCurrency()->getCurrencyCode() .
            " to " .
            $this->getToCurrency()->getCurrencyCode() .
            ": CurrencyRate must include the base currency " .
            $amount->getCurrency()->getCurrencyCode()
        );
    }
}
