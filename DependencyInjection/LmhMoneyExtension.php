<?php

namespace Lmh\Bundle\MoneyBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Parser as YamlParser;

class LmhMoneyExtension extends Extension {

    public function load(array $configs, ContainerBuilder $container)
    {

        // load the default configuration
        $parser = new YamlParser();
        $defaultsFilename = __DIR__ . '/../Resources/config/currency-configuration.yml';
        $defaultConfigs = $parser->parse(file_get_contents($defaultsFilename));

        $configs = array_merge($defaultConfigs, $configs);

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('lmh_money.precision', $config['precision']);
        $container->setParameter('lmh_money.regions', $config['regions']);
        $container->setParameter('lmh_money.symbols', $config['symbols']);

        // load the services now that configurations have been loaded
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    public function getAlias()
    {
        return 'lmh_money';
    }
}
