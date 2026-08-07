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
        if (!$transport) {
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

        $container->setParameter('zabbix_api.base_uri', $this->getConfigValue($config, 'base_uri'));
        $container->setParameter('zabbix_api.api_token', $this->getConfigValue($config, 'api_token'));
        $container->setParameter('zabbix_api.username', $this->getConfigValue($config, 'username'));
        $container->setParameter('zabbix_api.password', $this->getConfigValue($config, 'password'));
        $container->setParameter('zabbix_api.auth_ttl', $this->getConfigValue($config, 'auth_ttl'));
        $container->setParameter('zabbix_api.app_name', $this->getConfigValue($config, 'app_name'));
        $container->setParameter('zabbix_api.host_group', $this->getConfigValue($config, 'host_group'));
        $container->setParameter('zabbix_api.dashboard_config_path', $this->getConfigValue($config, 'dashboard_config_path'));
        $container->setParameter('zabbix_api.setup_enabled', $this->getConfigValue($config, 'setup_enabled'));
        $container->setParameter('zabbix_api.messenger_transport', $this->getConfigValue($config, 'messenger_transport'));
        $container->setParameter('zabbix_api.setup_failure_cooldown_seconds', (int) $this->getConfigValue($config, 'setup_failure_cooldown_seconds'));

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
    }

    private function getConfigValue(array $config, string $key): array|bool|float|int|string|null
    {
        $value = $config[$key] ?? null;
        if ($value === null || is_array($value) || is_bool($value) || is_float($value) || is_int($value) || is_string($value)) {
            return $value;
        }
        return null;
    }
}
