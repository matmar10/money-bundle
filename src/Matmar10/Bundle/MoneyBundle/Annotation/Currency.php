<?php

namespace Matmar10\Bundle\MoneyBundle\Annotation;

use Matmar10\Bundle\MoneyBundle\Annotation\BaseCompositeProperty;
use Matmar10\Bundle\MoneyBundle\Annotation\CompositeProperty;
use ReflectionProperty;

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
    public $currencyCode = null;

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
    public function getMap(ReflectionProperty $reflectionProperty)
    {
        $currencyCodePropertyName = (is_null($this->currencyCode)) ?
            $reflectionProperty->getName() . 'CurrencyCode' :
            $this->currencyCode;
        return array(
            'currencyCode' => array(
                'fieldName' => $currencyCodePropertyName,
                'length' => 3,
                'nullable' => $this->nullable,
                'type' => 'string',
            ),
        );
    }

    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

}
