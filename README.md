lmh-money-bundle
================

Symfony2 Bundle wrapping common Money and Currency related needs such as integer-based math, currency codes, and money conversion.

Installation
------------
Add the package to your composer.json file: 
`{
    "require": {
        "guzzle/guzzle": "~3.1"
    }
}`

Example Usage
-------------

TODO: more useful examples

`
// must use a valid iso4217 currency code (with the exception of BTC for Bitcoin, as specified in the override configuration)
$usd = new Currency('USD', 5, 2);

$usdAmount1 = new Money($usd);
$usdAmount1->setAmountFloat(1.2345);

$usdAmount2 = new Money($usd);
$usdAmount2->setAmountFloat(1.2345);

$usdAmount1->isEqual($usdAmount2); // true(
`

