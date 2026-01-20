<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class ZabbixApiExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('zabbix_api.base_uri', $config['base_uri']);
        $container->setParameter('zabbix_api.api_token', $config['api_token']);
        $container->setParameter('zabbix_api.username', $config['username']);
        $container->setParameter('zabbix_api.password', $config['password']);
        $container->setParameter('zabbix_api.auth_ttl', $config['auth_ttl']);
    }

    public function getConfiguration(array $config, ContainerBuilder $container): ?Configuration
    {
        return new Configuration();
    }
}
