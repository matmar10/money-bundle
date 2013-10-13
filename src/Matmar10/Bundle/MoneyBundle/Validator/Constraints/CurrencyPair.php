<?php

namespace Matmar10\Bundle\MoneyBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CurrencyPair extends Constraint {

    public $invalidInstanceMessage = 'The value is not a valid CurrencyPairInterface instance';

    public function validatedBy()
    {
        return 'currency_pair_validator';
    }
}
