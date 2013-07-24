<?php

namespace Lmh\Bundle\MoneyBundle\Service;

use Lmh\Bundle\MoneyBundle\Entity\Currency;
use Lmh\Bundle\MoneyBundle\Entity\CurrencyPair;
use Lmh\Bundle\MoneyBundle\Entity\Money;
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
     * @return \Lmh\Bundle\MoneyBundle\Entity\CurrencyPair
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
     * @return \Lmh\Bundle\MoneyBundle\Entity\Money
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
     * @return \Lmh\Bundle\MoneyBundle\Entity\Currency
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

    protected function searchCurrency($currencyCodeOrCountryCode)
    {
        // search first in additional configured currencies (since those are likely important to the end user of the Bundle
        $currency = $this->searchAdditionalConfiguredCurrencies($currencyCodeOrCountryCode);

        // not present in the configured currencies, look it up in the default XML based currency configuration
        if(!$currency) {
            $currencyNode = $this->getCurrencyNodeData($currencyCodeOrCountryCode);

            // not found in default XML; unsupported
            if(!$currencyNode) {
                throw new InvalidArgumentException("Currency or country code '$currencyCodeOrCountryCode' is not supported: no matching code found in file '{$this->currencyConfigurationFilename}'.");
            }
            $currency = new Currency(
                $currencyNode->getAttribute('code'),
                (int)$currencyNode->getAttribute('calculationPrecision'),
                (int)$currencyNode->getAttribute('displayPrecision')
            );
            $symbol = $currencyNode->getAttribute('symbol');
            if($symbol) {
                $currency->setSymbol($symbol);
            }
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
        // check if the requested code exists as a configured currency code
        if(false === array_key_exists($currencyCodeOrCountryCode, $this->extraCurrencies)) {
            $code = false;
            foreach($this->extraCurrencies as $currencyCode => $currencyData) {
                if(false === array_key_exists('regions', $currencyData)) {
                    continue;
                }
                if(false === array_search($currencyCodeOrCountryCode, $currencyData['regions'])) {
                    continue;
                }
                $code = $currencyCode;
            }

            // code not found from config, check if one was added manually
            if(!$code) {
                // code not found, unsupported
                return false;
            }
        } else {
            // requested code existed as a currency code
            $code = $currencyCodeOrCountryCode;
        }

        $calculationPrecision = $this->extraCurrencies[$code]['calculationPrecision'];
        $displayPrecision = $this->extraCurrencies[$code]['displayPrecision'];
        $symbol = $this->extraCurrencies[$code]['symbol'];

        return new Currency($code, $calculationPrecision, $displayPrecision, $symbol);
    }
}
