<?php

namespace Matmar10\Bundle\MoneyBundle\Annotation;

use Matmar10\Bundle\MoneyBundle\Annotation\BaseCompositeProperty;
use Matmar10\Bundle\MoneyBundle\Annotation\CompositeProperty;

/**
 * Currency annotation
 *
 * @bundle matmar10-money-bundle
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Currency extends BaseCompositeProperty implements CompositeProperty
{
    public $currencyCode;

    /**
     * {inheritDoc}
     */
    public function getClass()
    {
        return '\\Matmar10\\Money\\Entity\\Currency';
    }

    /**
     * {inheritDoc}
     */
    public function getMap()
    {
        return array(
            'currencyCode' => $this->currencyCode,
        );
    }

    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

}
