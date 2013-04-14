<?php

namespace Lmh\Bundle\MoneyBundle\Service;

use Symfony\Component\Yaml\Parser;
use Lmh\Bundle\MoneyBundle\Entity\Currency;
use Lmh\Bundle\MoneyBundle\Entity\CurrencyPair;
use Lmh\Bundle\MoneyBundle\Entity\Money;
use Lmh\Bundle\MoneyBundle\Exception\InvalidArgumentException;

class CurrencyManager
{

    protected static $configurationFilename;
    protected static $currencyData = array();

    /**
     * Construct a new service builder
     *
     * @param string $configurationFilename The filename of the configuration file
     */
    public function __construct($configurationFilename)
    {
        self::$configurationFilename = $configurationFilename;
        $parser = new Parser();
        self::$currencyData = $parser->parse(file_get_contents($configurationFilename));
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

        if(false === array_key_exists($currencyCode, self::$currencyData['precision'])) {
            throw new InvalidArgumentException("Currency '$currencyCode' is not supported: no currency precision settings found.");
        }

        $precisionData = self::$currencyData['precision'][$currencyCode];
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
            if(false === array_key_exists($currencyOrCountryCode, self::$currencyData['region'])) {
                throw new InvalidArgumentException("Currency for '$currencyOrCountryCode' is not supported: no country to currency mapping information found in '" . self::$configurationFilename . "'.");
            }
            return self::$currencyData['region'][$currencyOrCountryCode];
        }

        if(false === array_key_exists($currencyOrCountryCode, self::$currencyData['precision'])) {
            throw new InvalidArgumentException("Currency '$currencyOrCountryCode' is not supported: no currency precision information found in '" . self::$configurationFilename . "'.");
        }

        return $currencyOrCountryCode;
    }

    protected function lookupCurrencySymbol($currencyCode)
    {
        if(false === array_key_exists($currencyCode, self::$currencyData['symbol'])) {
            return '';
        }
        return self::$currencyData['symbol'][$currencyCode];
    }
}
