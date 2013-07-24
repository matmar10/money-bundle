<?php

namespace Lmh\Bundle\MoneyBundle\DependencyInjection;

use SimpleXMLElement;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class LmhMoneyExtension extends Extension {

    public function load(array $configs, ContainerBuilder $container)
    {

        // load the default configuration
        $defaultsFilename = __DIR__ . '/../Resources/config/currency-configuration.xml';
        $defaultConfigs = array(
            'lmh_money' => array(
                'currency_configuration_filename' => $defaultsFilename,
            ),
        );

        $configs = array_merge($defaultConfigs, $configs);

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('lmh_money.currency_configuration_filename', $config['currency_configuration_filename']);
        $container->setParameter('lmh_money.currencies', $config['currencies']);

        // load the services now that configurations have been loaded
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    public function getAlias()
    {
        return 'lmh_money';
    }
}
