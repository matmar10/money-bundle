<?php

namespace Matmar10\Bundle\MoneyBundle\Validator;

use Matmar10\Money\Entity\ExchangeRateInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class ExchangeRateValidator extends ConstraintValidator {

    public static $currencyManager;

    public function validate($value, Constraint $constraint)
    {
        if(!($value instanceof ExchangeRateInterface)) {
            $this->context->addViolation($constraint->invalidInstanceMessage);
        }
    }
}
