<?php

namespace Lmh\Bundle\MoneyBundle\Service;

use Symfony\Component\Yaml\Parser;
use Lmh\Bundle\MoneyBundle\Entity\Currency;
use Lmh\Bundle\MoneyBundle\Entity\CurrencyPair;
use Lmh\Bundle\MoneyBundle\Entity\Money;
use Lmh\Bundle\MoneyBundle\Exception\InvalidArgumentException;

class CurrencyManager
{

    protected static $precision;
    protected static $regions;
    protected static $symbols;

    /**
     * Construct a new service builder
     *
     * @param string $configurationFilename The filename of the configuration file
     */
    public function __construct(
        array $precision,
        array $regions,
        array $symbols
    )
    {
        self::$precision = $precision;
        self::$regions = $regions;
        self::$symbols = $symbols;
    }

    public function getCurrencyPair($fromCurrencyOrCountryCode, $toCurrencyOrCountryCode, $multiplier)
    {
        $from = $this->getCurrency($fromCurrencyOrCountryCode);
        $to = $this->getCurrency($toCurrencyOrCountryCode);
        return new CurrencyPair($from, $to, $multiplier);
    }

    public function getMoney($currencyOrCountryCode)
    {
        $currency = $this->getCurrency($currencyOrCountryCode);
        return new Money($currency);
    }

    public function getCurrency($currencyOrCountryCode)
    {
        $currencyCode = $this->lookupCurrencyCode($currencyOrCountryCode);

        if(false === array_key_exists($currencyCode, self::$precision)) {
            throw new InvalidArgumentException("Currency '$currencyCode' is not supported: no currency precision settings found.");
        }

        $precisionData = self::$precision[$currencyCode];
        $code = $this->getCode($currencyCode);
        $currency = new Currency(
            $code,
            $precisionData['calculation'],
            $precisionData['display']
        );
        $symbol = $this->lookupCurrencySymbol($currencyCode);
        $currency->setSymbol($symbol);

        return $currency;
    }

    public function getCode($currencyOrCountryCode)
    {
        return $this->lookupCurrencyCode($currencyOrCountryCode);
    }


    protected function lookupCurrencyCode($currencyOrCountryCode)
    {
        if(2 === strlen($currencyOrCountryCode)) {
            if(false === array_key_exists($currencyOrCountryCode, self::$regions)) {
                throw new InvalidArgumentException("Currency for '$currencyOrCountryCode' is not supported: no country to currency mapping information found.");
            }
            return self::$regions[$currencyOrCountryCode];
        }

        if(false === array_key_exists($currencyOrCountryCode, self::$precision)) {
            throw new InvalidArgumentException("Currency '$currencyOrCountryCode' is not supported: no currency precision information found.");
        }

        return $currencyOrCountryCode;
    }

    protected function lookupCurrencySymbol($currencyCode)
    {
        if(false === array_key_exists($currencyCode, self::$symbols)) {
            return '';
        }
        return self::$symbols[$currencyCode];
    }
}
