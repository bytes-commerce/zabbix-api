<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Tests;

use BytesCommerce\ZabbixApi\AuditLog;
use BytesCommerce\ZabbixApi\ZabbixClientInterface;
use DateTime;
use PHPUnit\Framework\TestCase;

final class AuditLogTest extends TestCase
{
    private ZabbixClientInterface $zabbixClient;

    private AuditLog $auditLog;

    protected function setUp(): void
    {
        $this->zabbixClient = $this->createMock(ZabbixClientInterface::class);
        $this->auditLog = new AuditLog($this->zabbixClient);
    }

    public function testGetWithDefaultOutputAndSelectDetails(): void
    {
        $params = ['userids' => '1'];
        $expectedParams = [
            'userids' => '1',
            'output' => 'extend',
            'selectDetails' => 'extend'
        ];
        $expectedResult = [['auditid' => '1', 'clock' => '1672531200']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('auditlog.get', $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->auditLog->get($params);

        self::assertSame($expectedResult, $result);
    }

    public function testGetWithCustomOutputAndSelectDetails(): void
    {
        $params = [
            'output' => ['auditid', 'clock', 'resourcename'],
            'selectDetails' => ['field', 'oldvalue', 'newvalue'],
            'filter' => ['resourcetype' => 4, 'action' => 1]
        ];
        $expectedResult = [['auditid' => '1', 'clock' => '1672531200', 'resourcename' => 'Test Host']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('auditlog.get', $params)
            ->willReturn($expectedResult);

        $result = $this->auditLog->get($params);

        self::assertSame($expectedResult, $result);
    }

    public function testGetWithDateTimeConversion(): void
    {
        $timeFrom = new DateTime('2023-01-01 00:00:00');
        $timeTill = new DateTime('2023-01-02 00:00:00');
        $params = ['time_from' => $timeFrom, 'time_till' => $timeTill];
        $expectedParams = [
            'time_from' => 1672531200,
            'time_till' => 1672617600,
            'output' => 'extend',
            'selectDetails' => 'extend'
        ];
        $expectedResult = [['auditid' => '1']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('auditlog.get', $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->auditLog->get($params);

        self::assertSame($expectedResult, $result);
    }

    public function testGetWithUnixTimestamps(): void
    {
        $params = ['time_from' => 1672531200, 'time_till' => 1672617600, 'resourcetype' => 4];
        $expectedParams = [
            'time_from' => 1672531200,
            'time_till' => 1672617600,
            'resourcetype' => 4,
            'output' => 'extend',
            'selectDetails' => 'extend'
        ];
        $expectedResult = [['auditid' => '1']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('auditlog.get', $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->auditLog->get($params);

        self::assertSame($expectedResult, $result);
    }

    public function testGetWithResourceTypeAndAction(): void
    {
        $params = ['resourcetype' => 4, 'action' => 1];
        $expectedParams = [
            'resourcetype' => 4,
            'action' => 1,
            'output' => 'extend',
            'selectDetails' => 'extend'
        ];
        $expectedResult = [['auditid' => '1', 'resourcename' => 'Updated Host']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('auditlog.get', $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->auditLog->get($params);

        self::assertSame($expectedResult, $result);
    }
}
