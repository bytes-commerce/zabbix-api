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
                    ->defaultValue('%env(ZABBIX_API_TOKEN)%')
                    ->info('API token for authentication')
                ->end()
                ->scalarNode('username')
                    ->defaultValue('%env(ZABBIX_USERNAME)%')
                    ->info('Username for Zabbix authentication')
                ->end()
                ->scalarNode('password')
                    ->defaultValue('%env(ZABBIX_PASSWORD)%')
                    ->info('Password for Zabbix authentication')
                ->end()
                ->integerNode('auth_ttl')
                    ->defaultValue(3600)
                    ->info('Authentication TTL in seconds')
                ->end()
                ->scalarNode('app_name')
                    ->defaultValue('%env(APP_NAME)%')
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
            ->end();

        return $treeBuilder;
    }
}
