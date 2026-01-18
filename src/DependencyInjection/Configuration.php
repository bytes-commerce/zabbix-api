<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\DependencyInjection;

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
                    ->defaultValue('%env(ZABBIX_API_BASE_URI)%')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('api_token')
                    ->defaultValue('%env(ZABBIX_API_TOKEN)%')
                    ->defaultNull()
                ->end()
                ->scalarNode('username')
                    ->defaultValue('%env(ZABBIX_USERNAME)%')
                    ->defaultNull()
                ->end()
                ->scalarNode('password')
                    ->defaultValue('%env(ZABBIX_PASSWORD)%')
                    ->defaultNull()
                ->end()
                ->integerNode('auth_ttl')
                    ->defaultValue(3600)
                    ->min(60)
                ->end()
            ->end();

        return $treeBuilder;
    }
}
