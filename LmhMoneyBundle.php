<?php

namespace Lmh\Bundle\MoneyBundle;


use Lmh\Bundle\MoneyBundle\DependencyInjection\LmhMoneyExtension;
use Lmh\Bundle\MoneyBundle\DependencyInjection\Compiler\EntityFieldMapperCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class LmhMoneyBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->registerExtension(new LmhMoneyExtension());
        $container->addCompilerPass(new EntityFieldMapperCompilerPass());
    }
}
