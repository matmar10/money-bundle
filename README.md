matmar10/money-bundle
================

Symfony2 Bundle wrapping common Money and Currency related needs such as integer-based math, currency codes, and money conversion.

Installation
------------
Add the package to your composer.json file: 

    {
        "repositories": [
            {
                "type": "vcs",
                "url": "https://github.com/matmar10/money-bundle
            }
        ],
        "require": {
            "matmar10/money-bundle": "dev-master"
        }
    }



Example Usage
-------------

```PHP

// must use a valid iso4217 currency code 
// with the exception of BTC for Bitcoin, as specified in the override configuration
$usd = new Currency('USD', 5, 2);

$usdAmount1 = new Money($usd);
$usdAmount1->setAmountFloat(1.2345);

$usdAmount2 = new Money($usd);
$usdAmount2->setAmountFloat(1.2345);

$usdAmount1->isEqual($usdAmount2); // true

$eurAmount = new Money(new Currency('EUR', 2, 2));
$eurAmount->setAmountFloat(10);

// split the 10 euros into three equal parts using euro cents as the smallest unit
$shares = $eurAmount->allocate(array(1, 1, 1), 2);

$shares[0]->getAmountFloat(); // 3.34
$shares[1]->getAmountFloat(); // 3.33
$shares[2]->getAmountFloat(); // 3.33

```

