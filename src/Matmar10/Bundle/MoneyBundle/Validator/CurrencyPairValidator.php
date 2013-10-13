<?php

namespace Matmar10\Bundle\MoneyBundle\Validator;

use Matmar10\Money\Entity\CurrencyPairInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class CurrencyPairValidator extends ConstraintValidator {

    public static $currencyManager;

    public function validate($value, Constraint $constraint)
    {
        if(!($value instanceof CurrencyPairInterface)) {
            $this->context->addViolation($constraint->invalidInstanceMessage);
        }
    }
}
