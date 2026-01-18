<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Tests;

use BytesCommerce\ZabbixApi\Zabbix\Host;
use BytesCommerce\ZabbixApi\Zabbix\ZabbixApiException;
use BytesCommerce\ZabbixApi\Zabbix\ZabbixClientInterface;
use PHPUnit\Framework\TestCase;

final class HostTest extends TestCase
{
    private ZabbixClientInterface $zabbixClient;

    private Host $host;

    protected function setUp(): void
    {
        $this->zabbixClient = $this->createMock(ZabbixClientInterface::class);
        $this->host = new Host($this->zabbixClient);
    }

    public function testGetWithDefaultOutput(): void
    {
        $params = ['filter' => ['status' => 0]];
        $expectedParams = ['filter' => ['status' => 0], 'output' => 'extend'];
        $expectedResult = [['hostid' => '1', 'host' => 'Test Host']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('host.get', $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->host->get($params);

        self::assertSame($expectedResult, $result);
    }

    public function testGetWithCustomOutput(): void
    {
        $params = [
            'output' => ['hostid', 'host', 'name'],
            'selectInterfaces' => 'extend',
            'selectGroups' => 'extend'
        ];
        $expectedResult = [['hostid' => '1', 'host' => 'Test Host', 'name' => 'Test Host Display']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('host.get', $params)
            ->willReturn($expectedResult);

        $result = $this->host->get($params);

        self::assertSame($expectedResult, $result);
    }

    public function testCreateValid(): void
    {
        $hosts = [
            [
                'host' => 'Linux-Server-01',
                'groups' => [['groupid' => '2']],
                'interfaces' => [
                    [
                        'type' => 1,
                        'main' => 1,
                        'useip' => 1,
                        'ip' => '192.168.1.100',
                        'dns' => '',
                        'port' => '10050'
                    ]
                ],
                'templates' => [['templateid' => '10001']]
            ]
        ];
        $expectedResult = ['hostids' => ['10105']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('host.create', $hosts)
            ->willReturn($expectedResult);

        $result = $this->host->create($hosts);

        self::assertSame($expectedResult, $result);
    }

    public function testCreateInvalidMissingHost(): void
    {
        $hosts = [
            [
                'groups' => [['groupid' => '2']]
            ]
        ];

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Host creation requires host name and groups array');

        $this->host->create($hosts);
    }

    public function testCreateInvalidMissingGroups(): void
    {
        $hosts = [
            [
                'host' => 'Linux-Server-01'
            ]
        ];

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Host creation requires host name and groups array');

        $this->host->create($hosts);
    }

    public function testCreateInvalidGroupsNotArray(): void
    {
        $hosts = [
            [
                'host' => 'Linux-Server-01',
                'groups' => 'invalid'
            ]
        ];

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Host creation requires host name and groups array');

        $this->host->create($hosts);
    }

    public function testUpdateValid(): void
    {
        $hosts = [
            [
                'hostid' => '10105',
                'status' => 1
            ]
        ];
        $expectedResult = ['hostids' => ['10105']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('host.update', $hosts)
            ->willReturn($expectedResult);

        $result = $this->host->update($hosts);

        self::assertSame($expectedResult, $result);
    }

    public function testUpdateInvalidMissingHostId(): void
    {
        $hosts = [
            [
                'status' => 1
            ]
        ];

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Host update requires hostid');

        $this->host->update($hosts);
    }

    public function testDelete(): void
    {
        $hostIds = ['10105', '10106'];
        $expectedResult = ['hostids' => ['10105', '10106']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('host.delete', $hostIds)
            ->willReturn($expectedResult);

        $result = $this->host->delete($hostIds);

        self::assertSame($expectedResult, $result);
    }

    public function testMassAddValid(): void
    {
        $params = [
            'hosts' => [['hostid' => '10105'], ['hostid' => '10106']],
            'templates' => [['templateid' => '10001']]
        ];
        $expectedResult = ['hostids' => ['10105', '10106']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('host.massadd', $params)
            ->willReturn($expectedResult);

        $result = $this->host->massAdd($params);

        self::assertSame($expectedResult, $result);
    }

    public function testMassAddInvalidMissingHosts(): void
    {
        $params = [
            'templates' => [['templateid' => '10001']]
        ];

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Mass add requires hosts array');

        $this->host->massAdd($params);
    }

    public function testMassUpdateValid(): void
    {
        $params = [
            'hosts' => [['hostid' => '10105'], ['hostid' => '10106']],
            'status' => 1
        ];
        $expectedResult = ['hostids' => ['10105', '10106']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('host.massupdate', $params)
            ->willReturn($expectedResult);

        $result = $this->host->massUpdate($params);

        self::assertSame($expectedResult, $result);
    }

    public function testMassUpdateInvalidMissingHosts(): void
    {
        $params = [
            'status' => 1
        ];

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Mass update requires hosts array');

        $this->host->massUpdate($params);
    }

    public function testMassRemoveValid(): void
    {
        $params = [
            'hostids' => ['10105', '10106'],
            'templateids' => ['10001']
        ];
        $expectedResult = ['hostids' => ['10105', '10106']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('host.massremove', $params)
            ->willReturn($expectedResult);

        $result = $this->host->massRemove($params);

        self::assertSame($expectedResult, $result);
    }

    public function testMassRemoveInvalidMissingHostIds(): void
    {
        $params = [
            'templateids' => ['10001']
        ];

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Mass remove requires hostids array');

        $this->host->massRemove($params);
    }
}
