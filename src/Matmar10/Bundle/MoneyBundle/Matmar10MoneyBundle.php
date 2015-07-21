<?php

namespace Matmar10\Bundle\MoneyBundle;

use Doctrine\DBAL\Types\Type;
use Matmar10\Bundle\MoneyBundle\DBAL\Type\CurrencyType;
use Matmar10\Bundle\MoneyBundle\DependencyInjection\Matmar10MoneyExtension;
use Matmar10\Bundle\MoneyBundle\DependencyInjection\CompositePropertyStrategiesCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class Matmar10MoneyBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->registerExtension(new Matmar10MoneyExtension());
        $container->addCompilerPass(new CompositePropertyStrategiesCompilerPass());
    }

    public function boot()
    {
        Type::addType(CurrencyType::NAME, CurrencyType::class);
        /** @var CurrencyType $currencyType */
        $currencyType = Type::getType(CurrencyType::NAME);

        /**
         * @todo maybe to expensiv ? alternate way?
         * @see http://www.emanueleminotto.it/service-injection-doctrine-dbal-type
         */
        $currencyType->setCurrencyManager(
            $this->container->get('matmar10_money.currency_manager')
        );
    }
}
