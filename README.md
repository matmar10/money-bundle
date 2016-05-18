Money Bundle
============

** MAINTAINER WANTER - issue reports and pull requests will not be actively investigated. I am happy to advise, but I'm not longer actively using/maintaing this library. Please let me know if you're interested in taking over ownership of the repo.

Overview
--------
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

Creating Objects
----------------

Currencies are identified by a currency code and have a calculation and display precision:

```PHP

$eur = new Currency('EUR', 2, 2);

$euros = new Money($eur);
$euros->setAmountFloat(1.99);

```

Basic Math
----------

The Money object wraps all basic math functions using underlying integer math
to avoid the (problems with floating point math)[http://stackoverflow.com/questions/3730019/why-not-use-double-or-float-to-represent-currency].

All amounts are stored as integer values internally using
the calculation precision as the scale.

```PHP

// must use a valid iso4217 currency code 
// with the exception of BTC for Bitcoin, as specified in the override configuration
$usd = new Currency('USD', 5, 2);

$usdAmount1 = new Money($usd);
$usdAmount1->setAmountFloat(1.2345);

$usdAmount2 = new Money($usd);
$usdAmount2->setAmountFloat(1.2345);

$usdAmount1->isEqual($usdAmount2); // true

$resultAmount1 = $usdAmount1->add($usdAmount2);
echo $resultAmount1->getAmountDisplay(); // 2.47

$resultAmount2 = $usdAmount1->subtract($usdAmount2);
echo $resultAmount2->getAmountFloat(); // 0

$resultAmount3 = $usdAmount1->multiply(3);
echo $resultAmount3->getAmountFloat(); // 3.7035
echo $resultAmount3->getAmountDisplay(); // 3.70

$resultAmount4 = $usdAmount1->divide(2);
echo $resultAmount3->getAmountFloat(); // 0.61725
echo $resultAmount3->getAmountDisplay(); // 0.62

```

Dealing with Fractional Cents
-----------------------------

How do you divide $10 evenly amongst three people?
In reality, you can't divide fractional cents.

Really, you want to end up with three _equal_-ish shares:

- $3.34
- $3.33
- $3.33


```PHP

$eurAmount = new Money(new Currency('EUR', 2, 2));
$eurAmount->setAmountFloat(10);

// split the 10 euros into three equal parts using euro cents as the smallest unit
$shares = $eurAmount->allocate(array(1, 1, 1), 2);

$shares[0]->getAmountFloat(); // 3.34
$shares[1]->getAmountFloat(); // 3.33
$shares[2]->getAmountFloat(); // 3.33

```

Converting Between Currencies
-----------------------------

Use the `CurrencyPair` object to convert between disparate currencies using an exchange rate:

Note that the rate works bi-directionally:

```PHP

$gbp = new Currency('GBP', 2, 2);
$usd = new Currency('USD', 2, 2);

$gbpAmount = new Money($gbp);
$gbpAmount->setAmountFloat(10);


// 1 GBP = 1.5 USD
$gbpToUsd = new CurrencyPair($gbp, $usd, 1.5);

$usdAmount = $gbpToUsd->convert($gbpAmount);
echo $usdAmount->getDisplay(); // 15.00

$gbpAmount2 = $gbpToUsd->convert($usdAmount);
echo $gbpAmount2->getDisplay(); // 10.00

```

Currency Manager Service
------------------------

Instead of building up currencies and money objects manually all the time,
consider using the `lmh_money.currency_manager` service that is registered
into Symfony's dependency injection container.

The manager supports providing an ISO country or currency code:

```PHP

// inside a Symfony controller, for example

$manager = $this->getContainer()->get('lmh_money.currency_manager');

$amount = $manager->getMoney('ES');
echo $amount->getCurrency(); // EUR
$amount->setAmountFloat(100);

// 1 USD = 0.75 EUR
$pair = $manager->getPair('US', 'ES', 0.75);

$converted = $pair->convert($amount);
echo $converted->getAmountDisplay(); // 75.00

```

Adding New Currencies
---------------------

You can add new currencies that are not supported by default.
This is useful, for example to add alternative currencies such as Litecoin and Ripple

```yaml

// inside a config file, such as app/config/config.yml

lmh_money:
    currencies:
        LTC: { displayPrecision: 5, calculationPrecision: 8, symbol: '&#0321;' }
        XRP: { displayPrecision: 8, calculationPrecision: 8 }

```

Currency Validator
------------------

The Bundle also includes a Symfony validator for use in validating an entity's attribute is a valid currency code
using (Symfony's Validator component)[https://github.com/symfony/Validator].

Example use of the annotation:

```php
<?php

// inside an entity file, such as src/Bundle/AcmeBundle/Entity/Purchase.php

namespace Acme\Bundle\AcmeBundle\Entity;

use Lmh\Bundle\MoneyBundle\Validator\Constraints as Assert;

class Purchase
{
    /**
     * @Assert\CurrencyCode()
     */
    public $currency;

}

```

Using in the validator (see (Validator Component docs for details)[https://github.com/symfony/Validator] ):


```php
<?php

use Acme\Bundle\AcmeBundle;
use Symfony\Component\Validator\Validation;

$validator = Validation::createValidatorBuilder()
    ->enableAnnotationMapping()
    ->getValidator();

$purchase = new Purchase()
$purchase->currency = 'invalid-this-is-not-a-code';
$violations = $validator->validate($purchase);
