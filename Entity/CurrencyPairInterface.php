<?php

namespace Lmh\Bundle\MoneyBundle\Entity;

use Lmh\Bundle\MoneyBundle\Entity\CurrencyInterface;

interface CurrencyPairInterface
{

    public function setFromCurrency(CurrencyInterface $fromCurrency);
    public function getFromCurrency();
    public function setToCurrency(CurrencyInterface $toCurrency);
    public function getToCurrency();

    /**
     * Compares whether this currency pair equals the supplied currency code
     *
     * @abstract
     * @param Lmh\Bundle\RateBundle\Entity\CurrencyPairInterface $currencyPair The currency pair to check against
     * @return boolean
     */
    public function equals(CurrencyPairInterface $currencyPair);

    /**
     * Checks whether the provided currency pair is the opposite of this pair
     *
     * @abstract
     * @param \Lmh\Bundle\RateBundle\Entity\CurrencyPair $currencyPair The currency pair to check against
     * @return boolean
     */
    public function isInverse(CurrencyPairInterface $currencyPair);

    /**
     * Returns the inverted representation of the current CurrencyPairInterface instance
     *
     * @abstract
     * @return \Lmh\Bundle\RateBundle\Entity\CurrencyPairInterface
     */
    public function getInverse();

    // compare as plain strings, ignoring precision
    static function currencyCodesMatch(CurrencyInterface $currency, CurrencyInterface $compareToCurrency);

    public function __toString();

}