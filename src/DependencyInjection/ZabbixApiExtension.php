<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class ZabbixApiExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        $transport = $config['messenger_transport'] ?? 'async';
        if (null === $transport || '' === $transport || false === $transport) {
            return;
        }

        $container->prependExtensionConfig('framework', [
            'messenger' => [
                'routing' => [
                    'BytesCommerce\ZabbixApi\Message\PushEventMessage' => $transport,
                    'BytesCommerce\ZabbixApi\Message\PushMetricMessage' => $transport,
                    'BytesCommerce\ZabbixApi\Message\EnsureZabbixSetupMessage' => $transport,
                ],
            ],
        ]);
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('zabbix_api.base_uri', $config['base_uri']);
        $container->setParameter('zabbix_api.api_token', $config['api_token']);
        $container->setParameter('zabbix_api.username', $config['username']);
        $container->setParameter('zabbix_api.password', $config['password']);
        $container->setParameter('zabbix_api.auth_ttl', $config['auth_ttl']);
        $container->setParameter('zabbix_api.app_name', $config['app_name']);
        $container->setParameter('zabbix_api.host_group', $config['host_group']);
        $container->setParameter('zabbix_api.dashboard_config_path', $config['dashboard_config_path']);
        $container->setParameter('zabbix_api.setup_enabled', $config['setup_enabled']);
        $container->setParameter('zabbix_api.messenger_transport', $config['messenger_transport']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
    }
}
