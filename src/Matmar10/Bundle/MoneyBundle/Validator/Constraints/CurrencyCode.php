<?php

namespace Matmar10\Bundle\MoneyBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CurrencyCode extends Constraint
{

    public $unsupportedMessage = 'The currency code "%code%" is invalid: that currency code is not supported.';
    public $invalidLengthMessage = 'The currency code "%code%" is invalid: currency code must be three (3) characters.';

    public function validatedBy()
    {
        return 'currency_code_validator';
    }
}
