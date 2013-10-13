<?php

namespace Matmar10\Bundle\MoneyBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Money extends Constraint {

    public $invalidInstanceMessage = 'The value is not a valid MoneyInterface instance';

    public function validatedBy()
    {
        return 'money_validator';
    }
}
