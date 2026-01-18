<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Tests;

use BytesCommerce\ZabbixApi\Zabbix\Alert;
use BytesCommerce\ZabbixApi\Zabbix\ZabbixClientInterface;
use DateTime;
use PHPUnit\Framework\TestCase;

final class AlertTest extends TestCase
{
    private ZabbixClientInterface $zabbixClient;

    private Alert $alert;

    protected function setUp(): void
    {
        $this->zabbixClient = $this->createMock(ZabbixClientInterface::class);
        $this->alert = new Alert($this->zabbixClient);
    }

    public function testGetWithDefaultOutput(): void
    {
        $params = ['userids' => '5'];
        $expectedParams = ['userids' => '5', 'output' => 'extend'];
        $expectedResult = [['alertid' => '1', 'subject' => 'Test Alert']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('alert.get', $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->alert->get($params);

        self::assertSame($expectedResult, $result);
    }

    public function testGetWithCustomOutput(): void
    {
        $params = ['output' => ['alertid', 'subject', 'message'], 'status' => 1];
        $expectedResult = [['alertid' => '1', 'subject' => 'Test Alert', 'message' => 'Test Message']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('alert.get', $params)
            ->willReturn($expectedResult);

        $result = $this->alert->get($params);

        self::assertSame($expectedResult, $result);
    }

    public function testGetWithDateTimeConversion(): void
    {
        $timeFrom = new DateTime('2023-01-01 00:00:00');
        $timeTill = new DateTime('2023-01-02 00:00:00');
        $params = ['time_from' => $timeFrom, 'time_till' => $timeTill];
        $expectedParams = ['time_from' => 1672531200, 'time_till' => 1672617600, 'output' => 'extend'];
        $expectedResult = [['alertid' => '1']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('alert.get', $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->alert->get($params);

        self::assertSame($expectedResult, $result);
    }

    public function testGetWithUnixTimestamps(): void
    {
        $params = ['time_from' => 1672531200, 'time_till' => 1672617600];
        $expectedParams = ['time_from' => 1672531200, 'time_till' => 1672617600, 'output' => 'extend'];
        $expectedResult = [['alertid' => '1']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('alert.get', $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->alert->get($params);

        self::assertSame($expectedResult, $result);
    }
}
