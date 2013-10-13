<?php

namespace Matmar10\Bundle\MoneyBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Currency extends Constraint {

    public $invalidInstanceMessage = 'The value is not a valid CurrencyInterface instance';

    public function validatedBy()
    {
        return 'currency_validator';
    }
}
