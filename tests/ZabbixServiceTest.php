<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Tests;

use BytesCommerce\ZabbixApi\Actions\AbstractAction;
use BytesCommerce\ZabbixApi\Actions\Host;
use BytesCommerce\ZabbixApi\Enums\ZabbixAction;
use BytesCommerce\ZabbixApi\ZabbixApiException;
use BytesCommerce\ZabbixApi\ZabbixClientInterface;
use BytesCommerce\ZabbixApi\ZabbixService;
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
            ->with(ZabbixAction::APIINFO_VERSION, [])
            ->willReturn('7.0.0');

        $version = $this->zabbixService->getApiVersion();

        self::assertSame('7.0.0', $version);
    }

    public function testGetApiVersionInvalidResponse(): void
    {
        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::APIINFO_VERSION, [])
            ->willReturn(['version' => '7.0.0']);

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Invalid API version response');

        $this->zabbixService->getApiVersion();
    }

    public function testCheckHealth(): void
    {
        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::APIINFO_VERSION, [])
            ->willReturn('7.0.0');

        $result = $this->zabbixService->checkHealth();

        self::assertTrue($result);
    }

    public function testActionReturnsCorrectInstance(): void
    {
        $action = $this->zabbixService->action(Host::class);

        self::assertInstanceOf(Host::class, $action);
        self::assertInstanceOf(AbstractAction::class, $action);
    }

    public function testActionThrowsOnInvalidClass(): void
    {
        $this->expectException(ZabbixApiException::class);

        $this->zabbixService->action(\stdClass::class);
    }
}
