<?php

namespace Matmar10\Bundle\MoneyBundle\Validator;

use Matmar10\Money\Entity\MoneyInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class MoneyValidator extends ConstraintValidator {

    public static $currencyManager;

    public function validate($value, Constraint $constraint)
    {
        if(!($value instanceof MoneyInterface)) {
            $this->context->addViolation($constraint->invalidInstanceMessage);
        }
    }
}
