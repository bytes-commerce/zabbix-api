<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Tests\Integration;

use BytesCommerce\ZabbixApi\Zabbix\ZabbixClientInterface;
use BytesCommerce\ZabbixApi\Zabbix\ZabbixServiceInterface;
use BytesCommerce\ZabbixApi\ZabbixApiBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

final class ZabbixApiBundleTest extends TestCase
{
    public function testServicesAreRegistered(): void
    {
        $container = new ContainerBuilder(new ParameterBag([
            'kernel.debug' => false,
            'kernel.environment' => 'test',
        ]));

        $bundle = new ZabbixApiBundle();
        $bundle->build($container);

        $extension = $bundle->getContainerExtension();
        $extension->load([], $container);

        $container->compile();

        self::assertTrue($container->has(ZabbixClientInterface::class));
        self::assertTrue($container->has(ZabbixServiceInterface::class));

        $client = $container->get(ZabbixClientInterface::class);
        self::assertInstanceOf(ZabbixClientInterface::class, $client);

        $service = $container->get(ZabbixServiceInterface::class);
        self::assertInstanceOf(ZabbixServiceInterface::class, $service);
    }
}
