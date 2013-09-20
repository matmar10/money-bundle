<?php

namespace Lmh\Bundle\MoneyBundle\Entity;

use JMS\Serializer\Annotation\AccessType;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\ReadOnly;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use Lmh\Bundle\MoneyBundle\Entity\Currency;
use Lmh\Bundle\MoneyBundle\Entity\CurrencyPairInterface;

/**
 * @AccessType("public_method")
 * @ExclusionPolicy("none")
 */
class BaseCurrencyPair implements CurrencyPairInterface
{

    /**
     * @Type("Lmh\Bundle\MoneyBundle\Currency\Currency")
     * @SerializedName("fromCurrency")
     */
    protected $fromCurrency;

    /**
     * @Type("Lmh\Bundle\MoneyBundle\Currency\Currency")
     * @SerializedName("toCurrency")
     */
    protected $toCurrency;

    public function __construct(CurrencyInterface $fromCurrency, CurrencyInterface $toCurrency) {
        $this->fromCurrency = $fromCurrency;
        $this->toCurrency = $toCurrency;
    }

    public function setFromCurrency(CurrencyInterface $fromCurrency)
    {
        $this->fromCurrency = $fromCurrency;
    }

    public function getFromCurrency()
    {
        return $this->fromCurrency;
    }

    public function setToCurrency(CurrencyInterface $toCurrency)
    {
        $this->toCurrency = $toCurrency;
    }

    public function getToCurrency()
    {
        return $this->toCurrency;
    }

    /**
     * Compares whether this currency pair equals the supplied currency code
     *
     * @param \Lmh\Bundle\RateBundle\Entity\CurrencyPair $currencyPair The currency pair to check against
     * @return boolean
     */
    public function equals(CurrencyPairInterface $currencyPair)
    {
        return self::currencyCodesMatch($this->fromCurrency, $currencyPair->getFromCurrency()) &&
            self::currencyCodesMatch($this->toCurrency, $currencyPair->getToCurrency());
    }

    /**
     * Checks whether the provided currency pair is the opposite of this pair
     *
     * @param \Lmh\Bundle\RateBundle\Entity\CurrencyPair $currencyPair The currency pair to check against
     * @return boolean
     */
    public function isInverse(CurrencyPairInterface $currencyPair)
    {
        return self::currencyCodesMatch($this->fromCurrency, $currencyPair->getToCurrency()) &&
            self::currencyCodesMatch($this->toCurrency, $currencyPair->getFromCurrency());
    }

    public function getInverse()
    {
        $className = get_class($this);
        if(!is_null($this->multiplier)) {
            return new $className($this->toCurrency, $this->fromCurrency, 1 / $this->multiplier);
        }
        return new $className($this->toCurrency, $this->fromCurrency);
    }

    // compare as plain strings, ignoring precision
    static function currencyCodesMatch(CurrencyInterface $currency, CurrencyInterface $compareToCurrency)
    {
        return $currency->getCurrencyCode() === $compareToCurrency->getCurrencyCode();

    }

    public function __toString()
    {
        return (string)$this->getFromCurrency() . ":" . (string)$this->getToCurrency();
    }
}
