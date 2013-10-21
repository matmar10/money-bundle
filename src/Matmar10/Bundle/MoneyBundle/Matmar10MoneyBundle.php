<?php

namespace Matmar10\Bundle\MoneyBundle;


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
}
