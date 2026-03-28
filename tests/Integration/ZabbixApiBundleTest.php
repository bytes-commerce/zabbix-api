<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Tests\Integration;

use BytesCommerce\ZabbixApi\ActionServiceInterface;
use BytesCommerce\ZabbixApi\ZabbixClientInterface;
use BytesCommerce\ZabbixApi\ZabbixServiceInterface;
use BytesCommerce\ZabbixApi\ZabbixApiBundle;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ZabbixApiBundleTest extends TestCase
{
    public function testServicesAreRegistered(): void
    {
        $container = new ContainerBuilder(new ParameterBag([
            'kernel.debug' => false,
            'kernel.environment' => 'test',
            'kernel.project_dir' => __DIR__ . '/../..',
            'env(ZABBIX_API_URL)' => 'https://zabbix.test/api_jsonrpc.php',
            'env(APP_NAME)' => 'TestApp',
        ]));

        $container->setDefinition('http_client', (new Definition(HttpClientInterface::class))->setSynthetic(true));
        $container->setDefinition(LoggerInterface::class, (new Definition(LoggerInterface::class))->setSynthetic(true));
        $container->setDefinition('cache.app', (new Definition(CacheInterface::class))->setSynthetic(true));
        $container->setAlias(HttpClientInterface::class, 'http_client')->setPublic(false);
        $container->setAlias(CacheInterface::class, 'cache.app')->setPublic(false);

        $bundle = new ZabbixApiBundle();
        $bundle->build($container);

        $extension = $bundle->getContainerExtension();
        self::assertNotNull($extension);

        $extension->load([
            [
                'base_uri' => 'https://zabbix.test/api_jsonrpc.php',
                'username' => 'admin',
                'password' => 'secret',
            ],
        ], $container);

        $container->getDefinition(ZabbixClientInterface::class)->setPublic(true);
        $container->getDefinition(ZabbixServiceInterface::class)->setPublic(true);
        $container->getDefinition(ActionServiceInterface::class)->setPublic(true);

        $container->compile();

        self::assertTrue($container->has(ZabbixClientInterface::class));
        self::assertTrue($container->has(ZabbixServiceInterface::class));
        self::assertTrue($container->has(ActionServiceInterface::class));
    }

    public function testParametersAreSet(): void
    {
        $container = new ContainerBuilder(new ParameterBag([
            'kernel.debug' => false,
            'kernel.environment' => 'test',
            'kernel.project_dir' => __DIR__ . '/../..',
            'env(ZABBIX_API_URL)' => 'https://zabbix.test/api_jsonrpc.php',
            'env(APP_NAME)' => 'TestApp',
        ]));

        $container->setDefinition('http_client', (new Definition(HttpClientInterface::class))->setSynthetic(true));
        $container->setDefinition(LoggerInterface::class, (new Definition(LoggerInterface::class))->setSynthetic(true));
        $container->setDefinition('cache.app', (new Definition(CacheInterface::class))->setSynthetic(true));
        $container->setAlias(HttpClientInterface::class, 'http_client')->setPublic(false);
        $container->setAlias(CacheInterface::class, 'cache.app')->setPublic(false);

        $bundle = new ZabbixApiBundle();
        $bundle->build($container);

        $extension = $bundle->getContainerExtension();
        self::assertNotNull($extension);

        $extension->load([
            [
                'base_uri' => 'https://zabbix.test/api_jsonrpc.php',
                'username' => 'monitoring',
                'password' => 'pass123',
                'auth_ttl' => 7200,
            ],
        ], $container);

        self::assertSame('https://zabbix.test/api_jsonrpc.php', $container->getParameter('zabbix_api.base_uri'));
        self::assertSame('monitoring', $container->getParameter('zabbix_api.username'));
        self::assertSame('pass123', $container->getParameter('zabbix_api.password'));
        self::assertSame(7200, $container->getParameter('zabbix_api.auth_ttl'));
        self::assertNull($container->getParameter('zabbix_api.api_token'));
    }

    public function testDefaultConfigValues(): void
    {
        $container = new ContainerBuilder(new ParameterBag([
            'kernel.debug' => false,
            'kernel.environment' => 'test',
            'kernel.project_dir' => __DIR__ . '/../..',
            'env(ZABBIX_API_URL)' => 'https://zabbix.test/api_jsonrpc.php',
            'env(APP_NAME)' => 'TestApp',
        ]));

        $container->setDefinition('http_client', (new Definition(HttpClientInterface::class))->setSynthetic(true));
        $container->setDefinition(LoggerInterface::class, (new Definition(LoggerInterface::class))->setSynthetic(true));
        $container->setDefinition('cache.app', (new Definition(CacheInterface::class))->setSynthetic(true));
        $container->setAlias(HttpClientInterface::class, 'http_client')->setPublic(false);
        $container->setAlias(CacheInterface::class, 'cache.app')->setPublic(false);

        $bundle = new ZabbixApiBundle();
        $bundle->build($container);

        $extension = $bundle->getContainerExtension();
        self::assertNotNull($extension);

        $extension->load([
            [
                'base_uri' => 'https://zabbix.test/api_jsonrpc.php',
            ],
        ], $container);

        self::assertNull($container->getParameter('zabbix_api.username'));
        self::assertNull($container->getParameter('zabbix_api.password'));
        self::assertNull($container->getParameter('zabbix_api.api_token'));
        self::assertSame(3600, $container->getParameter('zabbix_api.auth_ttl'));
        self::assertSame('async', $container->getParameter('zabbix_api.messenger_transport'));
    }

    public function testMessengerRoutingIsPrepended(): void
    {
        $container = new ContainerBuilder(new ParameterBag([
            'kernel.debug' => false,
            'kernel.environment' => 'test',
            'kernel.project_dir' => __DIR__ . '/../..',
            'env(ZABBIX_API_URL)' => 'https://zabbix.test/api_jsonrpc.php',
            'env(APP_NAME)' => 'TestApp',
        ]));

        $bundle = new ZabbixApiBundle();
        $extension = $bundle->getContainerExtension();
        self::assertInstanceOf(PrependExtensionInterface::class, $extension);

        // Test default transport (async)
        $container->prependExtensionConfig('zabbix_api', []);
        $extension->prepend($container);

        $frameworkConfig = $container->getExtensionConfig('framework');
        self::assertCount(1, $frameworkConfig);
        self::assertSame('async', $frameworkConfig[0]['messenger']['routing']['BytesCommerce\ZabbixApi\Message\PushEventMessage']);

        // Test custom transport
        $container = new ContainerBuilder(new ParameterBag([
            'kernel.debug' => false,
            'kernel.environment' => 'test',
            'kernel.project_dir' => __DIR__ . '/../..',
            'env(ZABBIX_API_URL)' => 'https://zabbix.test/api_jsonrpc.php',
            'env(APP_NAME)' => 'TestApp',
        ]));
        $container->prependExtensionConfig('zabbix_api', ['messenger_transport' => 'sync']);
        $extension->prepend($container);

        $frameworkConfig = $container->getExtensionConfig('framework');
        self::assertSame('sync', $frameworkConfig[0]['messenger']['routing']['BytesCommerce\ZabbixApi\Message\PushEventMessage']);

        // Test disabled transport (false)
        $container = new ContainerBuilder(new ParameterBag([
            'kernel.debug' => false,
            'kernel.environment' => 'test',
            'kernel.project_dir' => __DIR__ . '/../..',
            'env(ZABBIX_API_URL)' => 'https://zabbix.test/api_jsonrpc.php',
            'env(APP_NAME)' => 'TestApp',
        ]));
        $container->prependExtensionConfig('zabbix_api', ['messenger_transport' => false]);
        $extension->prepend($container);

        $frameworkConfig = $container->getExtensionConfig('framework');
        self::assertEmpty($frameworkConfig);
    }
}
