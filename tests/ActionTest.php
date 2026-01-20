<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Tests;

use BytesCommerce\ZabbixApi\Actions\Action;
use BytesCommerce\ZabbixApi\Actions\Dto\GetActionResponseDto;
use BytesCommerce\ZabbixApi\ZabbixApiException;
use BytesCommerce\ZabbixApi\ZabbixClientInterface;
use PHPUnit\Framework\TestCase;

final class ActionTest extends TestCase
{
    private ZabbixClientInterface $zabbixClient;

    private Action $action;

    protected function setUp(): void
    {
        $this->zabbixClient = $this->createMock(ZabbixClientInterface::class);
        $this->action = new Action($this->zabbixClient);
    }

    public function testGetWithDefaultOutput(): void
    {
        $params = ['filter' => ['eventsource' => 0]];
        $expectedParams = ['filter' => ['eventsource' => 0], 'output' => 'extend'];
        $apiResult = [['actionid' => '1', 'name' => 'Test Action', 'eventsource' => 0, 'esc_period' => '1h']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('action.get', $expectedParams)
            ->willReturn($apiResult);

        $result = $this->action->get($params);

        self::assertInstanceOf(GetActionResponseDto::class, $result);
        self::assertCount(1, $result->actions);
        self::assertSame('1', $result->actions[0]->getActionid());
        self::assertSame('Test Action', $result->actions[0]->getName());
    }

    public function testGetWithCustomOutput(): void
    {
        $params = ['output' => ['actionid', 'name'], 'filter' => ['eventsource' => 0]];
        $apiResult = [['actionid' => '1', 'name' => 'Test Action', 'eventsource' => 0, 'esc_period' => '1h']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('action.get', $params)
            ->willReturn($apiResult);

        $result = $this->action->get($params);

        self::assertInstanceOf(GetActionResponseDto::class, $result);
        self::assertCount(1, $result->actions);
        self::assertSame('1', $result->actions[0]->getActionid());
        self::assertSame('Test Action', $result->actions[0]->getName());
    }

    public function testCreateValid(): void
    {
        $actions = [
            [
                'name' => 'Auto-notify Admin',
                'eventsource' => 0,
                'esc_period' => '1h',
                'operations' => [['operationtype' => 0, 'opmessage' => ['default_msg' => 1], 'opmessage_grp' => [['usrgrpid' => '7']]]]
            ]
        ];
        $expectedResult = ['actionids' => ['15']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('action.create', $actions)
            ->willReturn($expectedResult);

        $result = $this->action->create($actions);

        self::assertSame($expectedResult, $result);
    }

    public function testCreateInvalidMissingName(): void
    {
        $actions = [
            [
                'eventsource' => 0,
                'esc_period' => '1h',
                'operations' => []
            ]
        ];

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Action creation requires name, eventsource, esc_period, and operations');

        $this->action->create($actions);
    }

    public function testUpdateValid(): void
    {
        $actions = [
            [
                'actionid' => '15',
                'status' => 1
            ]
        ];
        $expectedResult = ['actionids' => ['15']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('action.update', $actions)
            ->willReturn($expectedResult);

        $result = $this->action->update($actions);

        self::assertSame($expectedResult, $result);
    }

    public function testUpdateInvalidMissingActionId(): void
    {
        $actions = [
            [
                'status' => 1
            ]
        ];

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Action update requires actionid');

        $this->action->update($actions);
    }

    public function testDelete(): void
    {
        $actionIds = ['17', '18'];
        $expectedResult = ['actionids' => ['17', '18']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('action.delete', $actionIds)
            ->willReturn($expectedResult);

        $result = $this->action->delete($actionIds);

        self::assertSame($expectedResult, $result);
    }
}
