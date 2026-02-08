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
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->info('Zabbix API endpoint URL, e.g. https://zabbix.example.com/api_jsonrpc.php')
                ->end()
                ->scalarNode('api_token')
                    ->defaultNull()
                    ->info('Zabbix API token for token-based authentication (alternative to username/password)')
                ->end()
                ->scalarNode('username')
                    ->defaultNull()
                    ->info('Zabbix username for login-based authentication')
                ->end()
                ->scalarNode('password')
                    ->defaultNull()
                    ->info('Zabbix password for login-based authentication')
                ->end()
                ->integerNode('auth_ttl')
                    ->defaultValue(3600)
                    ->min(60)
                    ->info('Authentication token cache TTL in seconds (minimum 60)')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
