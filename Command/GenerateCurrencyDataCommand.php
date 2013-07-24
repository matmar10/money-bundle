<?php 

namespace Lmh\Bundle\MoneyBundle\Command;

use Doctrine\ORM\Mapping as ORM;
use DOMDocument;
use RuntimeException;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Dumper as YmlDumper;
use Symfony\Component\Yaml\Parser as YmlParser;
use XmlReader;

class GenerateCurrencyDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('currency:generate-data')
            ->setDescription('Generates currency data; uses the currency region info from http://unicode.org/repos/cldr/trunk/common/supplemental/supplementalData.xml to determine the most appropriate currency for each country based on which currency was most recently issued.')
            ->addOption('input', null, InputOption::VALUE_OPTIONAL, 'The input filename.', dirname(__FILE__).'/../Resources/config/supplementalData.xml')
            ->addOption('output', null, InputOption::VALUE_OPTIONAL, 'The output filename.', dirname(__FILE__).'/../Resources/config/currency-configuration.yml')
            ->addOption('currency-precision-input', null, InputOption::VALUE_OPTIONAL, 'The output filename.', dirname(__FILE__).'/../Resources/config/currency-precision-overrides.yml')
            ->addOption('default-display-precision', null, InputOption::VALUE_OPTIONAL, 'The default display precision where none is specified for the currency.', 2)
            ->addOption('default-calculation-precision', null, InputOption::VALUE_OPTIONAL, 'The default calculation precision where none is specified for the currency.', 2);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $defaultDisplayPrecision = $input->getOption('default-display-precision');
        $defaultCalculationPrecision = $input->getOption('default-calculation-precision');

        $ymlDumper = new YmlDumper();
        $ymlParser = new YmlParser();

        $currencyPrecisionOverrides = $ymlParser->parse(file_get_contents($input->getOption('currency-precision-input')));
        $currencyPrecision = array();

        $xml = new XmlReader();
        $xml->open($input->getOption('input'));

        $currencies = array();
        $region = null;
        $latestDate = strtotime("-500 years");
        $latestCurrency = null;
        
        while($xml->read()) {

            if(XmlReader::ELEMENT !== $xml->nodeType) {
                continue;
            }

            if('info' === $xml->name) {
                $xml->moveToAttribute('iso4217');
                $currencyCode = $xml->value;
                $xml->moveToAttribute('digits');
                $digits = $xml->value;
                $currencyPrecision[$currencyCode] = array(
                    'display' => (integer)$digits,
                    'calculation' => $defaultCalculationPrecision,
                );
            }

            if('region' === $xml->name) {
                $xml->moveToAttribute('iso3166');

                // save previous territory
                $currencies[$region] = $latestCurrency;

                // reset the language stat for the new country
                $latestDate = strtotime("-500 years");
                $latestCurrency = null;

                // grab the new country
                $region = $xml->value;
            }

            if('currency' === $xml->name) {

                $xml->moveToAttribute('tender');
                $isTender = $xml->value;
                if('false' === $isTender) {
                    continue;
                }

                $xml->moveToAttribute('iso4217');
                $currencyNode = $xml->value;

                $xml->moveToAttribute('from');
                $currencyStartDate = strtotime($xml->value);

                if($currencyStartDate > $latestDate) {
                    $latestDate = $currencyStartDate;
                    $latestCurrency = $currencyNode;
                }
            }

        }

        // this is faster than checking every iteration if we're on the first one yet
        unset($currencies['']);

        $currencyPrecision = array_merge($currencyPrecision, $currencyPrecisionOverrides);
        foreach($currencies as $country => $currencyNode) {
            if(false === array_key_exists($currencyNode, $currencyPrecision)) {
                $currencyPrecision[$currencyNode] = array(
                    'display' => $defaultDisplayPrecision,
                    'calculation' => $defaultCalculationPrecision,
                );
            }
        }

        unset($currencyPrecision['']);
        
        ksort($currencyPrecision);

        $currencyData = array(
            'precision' => $currencyPrecision,
            'region' => $currencies,
            'symbol' => array(
                'EUR' => '&#8364;',
                'GBP' => '&#163;',
                'USD' => '&#36;',
            ),
        );


        $yml = $ymlDumper->dump($currencyData, 3);

        $outputFile = $input->getOption('output');
        $handle = fopen($outputFile, 'w');
        if(!$handle) {
            die("Couldn't open file '$outputFile' for writing.");
        }
        fwrite($handle, $yml);
        fclose($handle);

        $outputXmlFilename = __DIR__.'/../Resources/config/currency-configuration.xml';
        $outputXmlElement = new SimpleXMLElement('<currency-configuration/>');
        foreach($currencyData['precision'] as $code => $precisionData) {
            $currencyNode = $outputXmlElement->addChild('currency');
            $currencyNode->addAttribute('code', $code);
            $currencyNode->addAttribute('calculationPrecision', $precisionData['calculation']);
            $currencyNode->addAttribute('displayPrecision', $precisionData['display']);
            if(false !== array_key_exists($code, $currencyData['symbol'])) {
                $currencyNode->addAttribute('symbol', $currencyData['symbol'][$code]);
            }
            $regions = array_filter($currencyData['region'], function($regionCurrencyCode) use ($code) {
                 return $code === $regionCurrencyCode;
            });
            if(count($regions)) {
                $regionsNode = $currencyNode->children('regions');
                if(!$regionsNode) {
                    $regionsNode = $currencyNode->addChild('regions');
                }
                foreach($regions as $regionCode => $currencyCode) {
                    $regionNode = $regionsNode->addChild('region');
                    $regionNode->addAttribute('code', $regionCode);
                }
            }
        }
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($outputXmlElement->asXML());
        if(!$dom->save($outputXmlFilename)) {
            throw new RuntimeException("Could not save output file: '$outputXmlFilename'");
        }
        
    }

}