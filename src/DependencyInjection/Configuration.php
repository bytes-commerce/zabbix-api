<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('zabbix_api');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('base_uri')
                    ->defaultValue('%env(ZABBIX_API_URL)%')
                    ->info('Base URI for Zabbix API')
                ->end()
                ->scalarNode('api_token')
                    ->defaultNull()
                    ->info('Zabbix API token for token-based authentication (alternative to username/password)')
                ->end()
                ->scalarNode('username')
                    ->defaultNull()
                    ->info('Username for Zabbix authentication')
                ->end()
                ->scalarNode('password')
                    ->defaultNull()
                    ->info('Zabbix password for login-based authentication')
                    ->defaultNull()
                ->end()
                ->integerNode('auth_ttl')
                    ->defaultValue(3600)
                    ->info('Authentication TTL in seconds')
                ->end()
                ->scalarNode('app_name')
                    ->defaultNull()
                    ->info('Application name for monitoring')
                ->end()
                ->scalarNode('host_group')
                    ->defaultValue('Application Servers')
                    ->info('Default Zabbix host group')
                ->end()
                ->scalarNode('dashboard_config_path')
                    ->defaultValue('%kernel.project_dir%/config/zabbix/dashboards')
                    ->info('Path to dashboard configuration files')
                ->end()
                ->booleanNode('setup_enabled')
                    ->defaultFalse()
                    ->info('Enable Zabbix setup commands')
                ->end()
                ->scalarNode('messenger_transport')
                    ->defaultValue('async')
                    ->info('Messenger transport to use for Zabbix messages (e.g. async, sync, or false to use default routing)')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
