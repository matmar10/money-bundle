<?php

namespace Matmar10\Bundle\MoneyBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CurrencyPair extends Constraint
{

    public $instanceClass = 'Matmar10\\Money\\Entity\\CurrencyPairInterface';
    public $propertyName = '';
    public $message = 'The value for the property %propertyName% is not a valid %instanceClass% instance';

    public function validatedBy()
    {
        return 'currency_pair_validator';
    }
}
