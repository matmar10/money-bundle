<?php

namespace Matmar10\Bundle\MoneyBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ExchangeRate extends Constraint {

    public $invalidInstanceMessage = 'The value is not a valid ExchangeRateInterface instance';

    public function validatedBy()
    {
        return 'exchange_rate_validator';
    }
}
