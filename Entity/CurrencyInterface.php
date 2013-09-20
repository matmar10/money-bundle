<?php

namespace Lmh\Bundle\MoneyBundle\Entity;

interface CurrencyInterface
{

    const CURRENCY_CODE_LENGTH = 3;

    public function setCurrencyCode($currencyCode);
    public function getCurrencyCode();
    public function setPrecision($precision);
    public function getPrecision();
    public function setDisplayPrecision($precision);
    public function getDisplayPrecision();
    public function equals(CurrencyInterface $currency);
    public function setSymbol($symbol);
    public function getSymbol();
    public function __toString();

}
