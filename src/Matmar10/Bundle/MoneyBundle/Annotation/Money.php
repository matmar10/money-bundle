<?php

namespace Matmar10\Bundle\MoneyBundle\Annotation;

use Matmar10\Bundle\MoneyBundle\Annotation\BaseCompositeProperty;
use Matmar10\Bundle\MoneyBundle\Annotation\CompositeProperty;
use Matmar10\Bundle\MoneyBundle\Exception\InvalidArgumentException;

/**
 * Money
 *
 * @bundle matmar10-money-bundle
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Money extends BaseCompositeProperty implements CompositeProperty
{

    public $currencyCode;

    public $amountInteger;

    public $amountFloat;

    public $amountDisplay;

    /**
     * {inheritDoc}
     */
    public function getClass()
    {
        return '\\Matmar10\\Money\\Entity\\Money';
    }

    /**
     * {inheritDoc}
     */
    public function getMap()
    {
        $map = array(
            'currencyCode' => $this->currencyCode,
        );

        if(!is_null($this->amountInteger)) {
            $map['amountInteger'] = $this->amountInteger;
            return $map;
        }

        if(!is_null($this->amountFloat)) {
            $map['amountFloat'] = $this->amountFloat;
            return $map;
        }

        if(!is_null($this->amountDisplay)) {
            $map['amountDisplay'] = $this->amountDisplay;
            return $map;
        }

        $message = 'No amount field was provided for composite property of type %s (annotation requires either an amountInteger, amountFloat, or amountDisplay property mapping';
        throw new InvalidArgumentException(sprintf($message, $this->getClass()));
    }
}
