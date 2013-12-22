<?php

namespace Matmar10\Bundle\MoneyBundle\DependencyInjection;

use SimpleXMLElement;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class Matmar10MoneyExtension extends Extension {

    public function load(array $configs, ContainerBuilder $container)
    {

        // load the default configuration
        $defaultsFilename = __DIR__ . '/../Resources/config/currency-configuration.xml';
        $defaultConfigs = array(
            'matmar10_money' => array(
                'currency_configuration_filename' => $defaultsFilename,
            ),
        );

        $configs = array_merge($defaultConfigs, $configs);

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('matmar10_money.currency_configuration_filename', $config['currency_configuration_filename']);
        $container->setParameter('matmar10_money.currencies', $config['currencies']);

        // load the services now that configurations have been loaded
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // add doctrine subscriber tags to our subscriber for all user requested connections
        if(array_key_exists('doctrine_connections', $config)) {
            $compositePropertySubscriberDefinition = $container->getDefinition('matmar10_money.composite_property_subscriber');
            foreach($config['doctrine_connections'] as $connectionName) {
                $compositePropertySubscriberDefinition->addTag('doctrine.event_subscriber', array(
                    'connection' => $connectionName,
                ));
            }
        }
    }

    public function getAlias()
    {
        return 'matmar10_money';
    }
}
