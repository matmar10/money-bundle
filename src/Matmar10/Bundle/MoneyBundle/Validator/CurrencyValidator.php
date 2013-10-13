<?php

namespace Matmar10\Bundle\MoneyBundle\Validator;

use Matmar10\Money\Entity\CurrencyInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class CurrencyValidator extends ConstraintValidator {

    public static $currencyManager;

    public function validate($value, Constraint $constraint)
    {
        if(!($value instanceof CurrencyInterface)) {
            $this->context->addViolation($constraint->invalidInstanceMessage);
        }
    }
}
