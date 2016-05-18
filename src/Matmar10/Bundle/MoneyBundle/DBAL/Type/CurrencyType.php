<?php

namespace Matmar10\Bundle\MoneyBundle\DBAL\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Matmar10\Bundle\MoneyBundle\Service\CurrencyManager;
use Matmar10\Money\Entity\CurrencyInterface;
use Matmar10\Money\Exception\InvalidArgumentException;

/**
 * Description of CurrencyType
 *
 * @author Volker von Hoesslin <volker.von.hoesslin@empora.com>
 */
class CurrencyType extends Type {

    const NAME = 'currency';

    /**
     * @var CurrencyManager $currencyManager
     */
    private $currencyManager;

    /**
     * @param CurrencyManager $currencyManager
     */
    public function setCurrencyManager(CurrencyManager $currencyManager) {
        $this->currencyManager = $currencyManager;
    }

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array                                     $fieldDeclaration The field declaration.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, \Doctrine\DBAL\Platforms\AbstractPlatform $platform) {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * @param string           $value
     * @param AbstractPlatform $platform
     *
     * @return \Matmar10\Money\Entity\Currency
     */
    public function convertToPHPValue($value, AbstractPlatform $platform) {
        return $this->currencyManager->getCurrency($value);
    }

    /**
     * @param CurrencyInterface $value
     * @param AbstractPlatform  $platform
     *
     * @return string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        if ($value instanceof CurrencyInterface) {
            return (string)$value;
        }
        $given = !strcasecmp(gettype($value), 'object') ? get_class($value) : gettype($value);
        throw new InvalidArgumentException(sprintf('Expect instance of "%s", given "%s"', CurrencyInterface::class, $given));
    }

    /**
     * Gets the name of this type.
     *
     * @return string
     *
     * @todo Needed?
     */
    public function getName() {
        return static::NAME;
    }

    /**
     * @return CurrencyType
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function boot() {
        if (!static::hasType(static::NAME)) {
            static::addType(static::NAME, static::class);
        }
        return static::getType(static::NAME);
    }
}