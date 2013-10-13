<?php

namespace Matmar10\Bundle\MoneyBundle\Validator;

use Matmar10\Money\Entity\CurrencyInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class InstanceValidator extends ConstraintValidator {

    public function validate($value, Constraint $constraint)
    {
        if(!($value instanceof $constraint->instanceClass)) {
            $this->context->addViolation($constraint->message, array(
                '%instanceClass%' => $constraint->instanceClass,
                '%propertyName%' => $constraint->propertyName,
            ));
        }
    }
}
