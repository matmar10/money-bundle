<?php

namespace Lmh\Bundle\MoneyBundle\Entity;

use JMS\Serializer\Annotation\AccessType;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\ReadOnly;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use Lmh\Bundle\MoneyBundle\Entity\Currency;

/**
 * @AccessType("public_method")
 * @ExclusionPolicy("none")
 */
class ConversionRate
{

    /**
     * @Type("Lmh\Bundle\MoneyBundle\Currency\Currency")
     * @SerializedName("fromCurrency")
     */
    protected $fromCurrency;

    /**
     * @Type("Lmh\Bundle\MoneyBundle\Currency\Currency")
     * @SerializedName("toCurrency")
     */
    protected $toCurrency;

    /**
     * @Type("double")
     */
    protected $multiplier;

    public function __construct(Currency $fromCurrency, Currency $toCurrency, $multiplier) {
        $this->fromCurrency = $fromCurrency;
        $this->toCurrency = $toCurrency;
        $this->multiplier = $multiplier;
    }

    public function setFromCurrency($fromCurrency)
    {
        $this->fromCurrency = $fromCurrency;
    }

    public function getFromCurrency()
    {
        return $this->fromCurrency;
    }

    public function setMultiplier($multiplier)
    {
        $this->multiplier = $multiplier;
    }

    public function getMultiplier()
    {
        return $this->multiplier;
    }

    public function setToCurrency($toCurrency)
    {
        $this->toCurrency = $toCurrency;
    }

    public function getToCurrency()
    {
        return $this->toCurrency;
    }
}
