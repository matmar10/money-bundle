<?php

namespace Lmh\Bundle\MoneyBundle\Service;

use Matmar10\Money\Entity\Currency;
use Matmar10\Money\Entity\CurrencyPair;
use Matmar10\Money\Entity\Money;
use Lmh\Bundle\MoneyBundle\Exception\ConfigurationException;
use Lmh\Bundle\MoneyBundle\Exception\InvalidArgumentException;
use Symfony\Component\Yaml\Parser;
use XmlReader;

class CurrencyManager
{

    const CURRENCY_CODE_STRING_LENGTH = 3;

    protected $currencyConfigurationFilename;

    protected $extraCurrencies;

    protected $addedCurrencies = array();
    protected $addedCurrencyRegions = array();

    /**
     * Construct a new service builder
     *
     * @param string $configurationFilename The filename of the configuration file
     */
    public function __construct($currencyConfigurationFilename, array $extraCurrencyConfig = array())
    {
        $this->currencyConfigurationFilename = $currencyConfigurationFilename;
        $this->extraCurrencies = $extraCurrencyConfig;
    }

    /**
     * Build a CurrencyPair based on the provided from and to currencies at the specified multiplier rate
     *
     * @param string $fromCurrencyOrCountryCode From currency
     * @param string $toCurrencyOrCountryCode To Currency
     * @param float $multiplier The multiplier to convert the from currency to the to currency
     * @return \Matmar10\Money\Entity\CurrencyPair
     */
    public function getCurrencyPair($fromCurrencyOrCountryCode, $toCurrencyOrCountryCode, $multiplier)
    {
        $from = $this->getCurrency($fromCurrencyOrCountryCode);
        $to = $this->getCurrency($toCurrencyOrCountryCode);
        return new CurrencyPair($from, $to, $multiplier);
    }

    /**
     * Build a Money object based on the provided country or currency code
     *
     * @param string $currencyCodeOrCountryCode The currency or country code to build a Money object from
     * @return \Matmar10\Money\Entity\Money
     */
    public function getMoney($currencyCodeOrCountryCode)
    {
        $currency = $this->getCurrency($currencyCodeOrCountryCode);
        return new Money($currency);
    }

    /**
     * Get a Currency object for the provided country or currency code
     *
     * @param string $currencyCodeOrCountryCode The currency or country code to build a Currency object from
     * @return \Matmar10\Money\Entity\Currency
     * @throws \Lmh\Bundle\MoneyBundle\Exception\InvalidArgumentException
     */
    public function getCurrency($currencyCodeOrCountryCode)
    {
        return $this->searchCurrency($currencyCodeOrCountryCode);
    }

    /**
     * Get the currency code for the provided country or currency code
     *
     * @param string $currencyCodeOrCountryCode The currency or country code to retrieve the currency code for
     * @return string
     * @throws \Lmh\Bundle\MoneyBundle\Exception\InvalidArgumentException
     */
    public function getCode($currencyCodeOrCountryCode)
    {
        $currency = $this->searchCurrency($currencyCodeOrCountryCode);
        return $currency->getCurrencyCode();
    }

    /**
     * Adds a new managed currency on the fly
     *
     * @param \Matmar10\Money\Entity\Currency $currency The Currency to add
     * @return \Lmh\Bundle\MoneyBundle\Service\CurrencyManager
     */
    public function addCurrency(Currency $currency, array $regions = array())
    {
        $this->addedCurrencies[$currency->getCurrencyCode()] = $currency;
        $this->addedCurrencyRegions[$currency->getCurrencyCode()] = $regions;
        return $this;
    }

    protected function searchCurrency($currencyCodeOrCountryCode)
    {
        // search first in the on-the-fly added currencies
        $currency = $this->searchAddedCurrencies($currencyCodeOrCountryCode);
        if(false !== $currency) {
            return $currency;
        }

        // search first in additional configured currencies (since those are likely important to the end user of the Bundle
        $currency = $this->searchAdditionalConfiguredCurrencies($currencyCodeOrCountryCode);
        if(false !== $currency) {
            return $currency;
        }

        // not present in the user configured currencies, look it up in the default XML based currency configuration
        $currencyNode = $this->getCurrencyNodeData($currencyCodeOrCountryCode);

        // not found in default XML; unsupported
        if(!$currencyNode) {
            throw new InvalidArgumentException("Currency or country code '$currencyCodeOrCountryCode' is not supported: no matching code found in file '{$this->currencyConfigurationFilename}'.");
        }

        // need to cast to int, since XML is string by default
        $currency = new Currency(
            $currencyNode->getAttribute('code'),
            (integer)$currencyNode->getAttribute('calculationPrecision'),
            (integer)$currencyNode->getAttribute('displayPrecision')
        );
        $symbol = $currencyNode->getAttribute('symbol');
        if($symbol) {
            $currency->setSymbol($symbol);
        }

        return $currency;
    }

    protected function getCurrencyNodeData($currencyCodeOrCountryCode)
    {
        $missingAttributeErrorMsg = "Invalid schema for configuration file '{$this->currencyConfigurationFilename}': missing attribute '%s'";
        $xml = new XmlReader();
        $xml->open($this->currencyConfigurationFilename);
        $lastCurrencyNode = null;
        while($xml->read()) {

            if(XmlReader::ELEMENT !== $xml->nodeType) {
                continue;
            }

            if('currency' === $xml->name) {
                if(!$xml->moveToAttribute('code')) {
                    throw new ConfigurationException(sprintf($missingAttributeErrorMsg, 'code'));
                }
                $lastCurrencyNode = $xml->expand();
                if($currencyCodeOrCountryCode === $xml->value) {
                    $xml->close();
                    return $lastCurrencyNode;
                }
            }

            if(self::CURRENCY_CODE_STRING_LENGTH === strlen($currencyCodeOrCountryCode)) {
                continue;
            }

            if('region' === $xml->name) {
                if(!$xml->moveToAttribute('code')) {
                    throw new ConfigurationException(sprintf($missingAttributeErrorMsg, 'region'));
                }
                if($currencyCodeOrCountryCode === $xml->value) {
                    $xml->close();
                    return $lastCurrencyNode;
                }
            }
        }

        $xml->close();
        return false;
    }

    protected function searchAdditionalConfiguredCurrencies($currencyCodeOrCountryCode)
    {
        // helper function to build new currency from meta ata
        $buildCurrencyCode = function($currencyData, $currencyCode) {
            $calculationPrecision = $currencyData[$currencyCode]['calculationPrecision'];
            $displayPrecision = $currencyData[$currencyCode]['displayPrecision'];
            $symbol = $currencyData[$currencyCode]['symbol'];
            return new Currency($currencyCode, $calculationPrecision, $displayPrecision, $symbol);
        };

        // search currency codes
        if(false !== array_key_exists($currencyCodeOrCountryCode, $this->extraCurrencies)) {
            return call_user_func($buildCurrencyCode, $this->extraCurrencies, $currencyCodeOrCountryCode);
        }

        // search regions
        foreach($this->extraCurrencies as $currencyCode => $currencyData) {
            // ignore if no regions set for this currency
            if(false === array_key_exists('regions', $currencyData)) {
                continue;
            }
            // ignore if no currency match
            if(false === array_search($currencyCodeOrCountryCode, $currencyData['regions'])) {
                continue;
            }

            // currency found
            return call_user_func($buildCurrencyCode, $this->extraCurrencies, $currencyCode);
        }

        // not found
        return false;
    }

    protected function searchAddedCurrencies($currencyCodeOrCountryCode)
    {
        // check if the requested code exists as a curerncy code
        if(false !== array_key_exists($currencyCodeOrCountryCode, $this->addedCurrencies)) {
            return $this->addedCurrencies[$currencyCodeOrCountryCode];
        }

        // didn't exist as a currency code, look up in regions
        foreach($this->addedCurrencyRegions as $currencyCode => $regions) {
            if(false !== array_search($currencyCodeOrCountryCode, $regions)) {
                return $this->addedCurrencies[$currencyCode];
            }
        }

        // didn't exist at all
        return false;
    }
}
