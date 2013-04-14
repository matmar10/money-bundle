<?php

namespace Lmh\Bundle\MoneyBundle\Entity;

use JMS\Serializer\Annotation\AccessType;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\ReadOnly;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use Lmh\Bundle\MoneyBundle\Exception\InvalidCurrencyCodeException;

/**
 * @AccessType("public_method")
 * @ExclusionPolicy("none")
 */
class Currency
{

    const CURRENCY_CODE_LENGTH = 3;

    /**
     * @Type("string")
     * @SerializedName("currencyCode")
     */
    protected $currencyCode;

    /**
     * @Type("integer")
     */
    protected $precision;
    
    /**
     * @Type("integer")
     * @SerializedName("displayPrecision")
     */
    protected $displayPrecision;

    /**
     * @Type("string")
     */
    protected $symbol;

    public function __construct($currencyCode, $precision, $displayPrecision) {
        $this->setCurrencyCode($currencyCode);
        $this->precision = $precision;
        $this->displayPrecision = $displayPrecision;
    }

    public function setCurrencyCode($currencyCode)
    {
        if(strlen($currencyCode) !== self::CURRENCY_CODE_LENGTH) {
            throw new InvalidCurrencyCodeException("Invalid currency code '$currencyCode' specified: currency codes must be " . self::CURRENCY_CODE_LENGTH .  "characters in length.");
        }
        $this->currencyCode = $currencyCode;
    }

    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    public function setPrecision($precision)
    {
        $this->precision = $precision;
    }

    public function getPrecision()
    {
        return $this->precision;
    }

    public function setDisplayPrecision($precision)
    {
        $this->displayPrecision = $precision;
    }

    public function getDisplayPrecision()
    {
        return $this->displayPrecision;
    }

    public function equals(Currency $currency) {
        return $this->currencyCode === $currency->getCurrencyCode() &&
                $this->precision === $currency->getPrecision() &&
                $this->displayPrecision === $currency->getDisplayPrecision();
    }

    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;
    }

    public function getSymbol()
    {
        return $this->symbol;
    }

    public function __toString() {
        return json_encode($this);
    }
}
