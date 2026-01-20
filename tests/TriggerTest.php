<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Tests;

use BytesCommerce\ZabbixApi\Trigger;
use BytesCommerce\ZabbixApi\ZabbixApiException;
use BytesCommerce\ZabbixApi\ZabbixClientInterface;
use PHPUnit\Framework\TestCase;

final class TriggerTest extends TestCase
{
    private ZabbixClientInterface $zabbixClient;

    private Trigger $trigger;

    protected function setUp(): void
    {
        $this->zabbixClient = $this->createMock(ZabbixClientInterface::class);
        $this->trigger = new Trigger($this->zabbixClient);
    }

    public function testGetWithDefaultOutput(): void
    {
        $params = ['filter' => ['value' => 1]];
        $expectedParams = ['filter' => ['value' => 1], 'output' => 'extend'];
        $expectedResult = [['triggerid' => '1', 'description' => 'Test Trigger']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('trigger.get', $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->trigger->get($params);

        self::assertSame($expectedResult, $result);
    }

    public function testGetWithCustomOutput(): void
    {
        $params = ['output' => ['triggerid', 'description', 'priority'], 'selectHosts' => ['hostid', 'name']];
        $expectedResult = [['triggerid' => '1', 'description' => 'Test Trigger', 'priority' => 4]];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('trigger.get', $params)
            ->willReturn($expectedResult);

        $result = $this->trigger->get($params);

        self::assertSame($expectedResult, $result);
    }

    public function testCreateValid(): void
    {
        $triggers = [
            [
                'description' => 'Processor load is too high on {HOST.NAME}',
                'expression' => 'last(/Linux server/system.cpu.load[percpu,avg1])>5',
                'priority' => 4,
                'dependencies' => [['triggerid' => '17367']],
                'tags' => [['tag' => 'service', 'value' => 'cpu']]
            ]
        ];
        $expectedResult = ['triggerids' => ['13938']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('trigger.create', $triggers)
            ->willReturn($expectedResult);

        $result = $this->trigger->create($triggers);

        self::assertSame($expectedResult, $result);
    }

    public function testCreateInvalidMissingDescription(): void
    {
        $triggers = [
            [
                'expression' => 'last(/Linux server/system.cpu.load[percpu,avg1])>5'
            ]
        ];

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Trigger creation requires description and expression');

        $this->trigger->create($triggers);
    }

    public function testCreateInvalidMissingExpression(): void
    {
        $triggers = [
            [
                'description' => 'Processor load is too high on {HOST.NAME}'
            ]
        ];

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Trigger creation requires description and expression');

        $this->trigger->create($triggers);
    }

    public function testUpdateValid(): void
    {
        $triggers = [
            [
                'triggerid' => '13938',
                'status' => 1,
                'priority' => 5
            ]
        ];
        $expectedResult = ['triggerids' => ['13938']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('trigger.update', $triggers)
            ->willReturn($expectedResult);

        $result = $this->trigger->update($triggers);

        self::assertSame($expectedResult, $result);
    }

    public function testUpdateInvalidMissingTriggerId(): void
    {
        $triggers = [
            [
                'status' => 1
            ]
        ];

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Trigger update requires triggerid');

        $this->trigger->update($triggers);
    }

    public function testDelete(): void
    {
        $triggerIds = ['17369', '17370'];
        $expectedResult = ['triggerids' => ['17369', '17370']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('trigger.delete', $triggerIds)
            ->willReturn($expectedResult);

        $result = $this->trigger->delete($triggerIds);

        self::assertSame($expectedResult, $result);
    }

    public function testCreateBulk(): void
    {
        $triggers = [
            [
                'description' => 'CPU Load High',
                'expression' => 'last(/host/system.cpu.load)>5'
            ],
            [
                'description' => 'Memory Usage High',
                'expression' => 'last(/host/vm.memory.size[available])<1000000'
            ]
        ];
        $expectedResult = ['triggerids' => ['1', '2']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('trigger.create', $triggers)
            ->willReturn($expectedResult);

        $result = $this->trigger->create($triggers);

        self::assertSame($expectedResult, $result);
    }
}
