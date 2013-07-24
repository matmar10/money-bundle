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

    protected $extraCurrencyConfig;

    /**
     * Construct a new service builder
     *
     * @param string $configurationFilename The filename of the configuration file
     */
    public function __construct($currencyConfigurationFilename, array $extraCurrencyConfig = array())
    {
        $this->currencyConfigurationFilename = $currencyConfigurationFilename;
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
        $currencyNode = $this->getCurrencyNodeData($currencyOrCountryCode);
        if(!$currencyNode) {
            throw new InvalidArgumentException("Currency or country code '$currencyOrCountryCode' is not supported: no matching code found in file '{$this->currencyConfigurationFilename}'.");
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

        return $currency;
    }

    public function getCode($currencyOrCountryCode)
    {
        $currencyNode = $this->getCurrencyNodeData($currencyOrCountryCode);
        if(!$currencyNode) {
            throw new InvalidArgumentException("Currency or country code '$currencyOrCountryCode' is not supported: no matching code found in file '{$this->currencyConfigurationFilename}'.");
        }
        return $currencyNode->getAttribute('code');
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
}
