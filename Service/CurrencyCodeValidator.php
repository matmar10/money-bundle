<?php

namespace Lmh\Bundle\MoneyBundle\Service;

use Lmh\Bundle\MoneyBundle\Exception\InvalidArgumentException;
use Lmh\Bundle\MoneyBundle\Service\CurrencyManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class CurrencyCodeValidator extends ConstraintValidator {

    public static $currencyManager;

    public function __construct(CurrencyManager $currencyManager)
    {
        self::$currencyManager = $currencyManager;
    }

    public function validate($value, Constraint $constraint)
    {
        if(3 !== strlen($value)) {
            $this->context->addViolation($constraint->invalidLengthMessage, array(
                '%code%' => $value
            ));
        }

        try {
            $code = self::$currencyManager->getCode($value);
        } catch(InvalidArgumentException $e) {
            $this->context->addViolation($constraint->unsupportedMessage, array(
                '%code%' => $value
            ));
        }

    }
}
