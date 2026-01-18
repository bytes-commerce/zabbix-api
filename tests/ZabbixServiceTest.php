<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Tests;

use BytesCommerce\ZabbixApi\Zabbix\ZabbixApiException;
use BytesCommerce\ZabbixApi\Zabbix\ZabbixClientInterface;
use BytesCommerce\ZabbixApi\Zabbix\ZabbixService;
use PHPUnit\Framework\TestCase;

final class ZabbixServiceTest extends TestCase
{
    private ZabbixClientInterface $zabbixClient;

    private ZabbixService $zabbixService;

    protected function setUp(): void
    {
        $this->zabbixClient = $this->createMock(ZabbixClientInterface::class);
        $this->zabbixService = new ZabbixService($this->zabbixClient);
    }

    public function testGetApiVersion(): void
    {
        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('apiinfo.version')
            ->willReturn('6.0.0');

        $version = $this->zabbixService->getApiVersion();

        self::assertSame('6.0.0', $version);
    }

    public function testGetApiVersionInvalidResponse(): void
    {
        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('apiinfo.version')
            ->willReturn(['version' => '6.0.0']);

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Invalid API version response');

        $this->zabbixService->getApiVersion();
    }

    public function testCheckHealth(): void
    {
        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('apiinfo.version')
            ->willReturn('6.0.0');

        $result = $this->zabbixService->checkHealth();

        self::assertTrue($result);
    }
}
