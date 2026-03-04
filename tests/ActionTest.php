<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Tests;

use BytesCommerce\ZabbixApi\Actions\Action;
use BytesCommerce\ZabbixApi\Actions\Dto\CreateActionDto;
use BytesCommerce\ZabbixApi\Actions\Dto\CreateSingleActionDto;
use BytesCommerce\ZabbixApi\Actions\Dto\DeleteActionDto;
use BytesCommerce\ZabbixApi\Actions\Dto\GetActionDto;
use BytesCommerce\ZabbixApi\Actions\Dto\GetActionResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\UpdateActionDto;
use BytesCommerce\ZabbixApi\Actions\Dto\UpdateSingleActionDto;
use BytesCommerce\ZabbixApi\Enums\EventSourceEnum;
use BytesCommerce\ZabbixApi\Enums\StatusEnum;
use BytesCommerce\ZabbixApi\Enums\ZabbixAction;
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
        $dto = new GetActionDto(filter: ['eventsource' => 0]);
        $expectedParams = ['filter' => ['eventsource' => 0], 'output' => 'extend'];
        $apiResult = [['actionid' => '1', 'name' => 'Test Action', 'eventsource' => 0, 'esc_period' => '1h']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::ACTION_GET, $expectedParams)
            ->willReturn($apiResult);

        $result = $this->action->get($dto);

        self::assertInstanceOf(GetActionResponseDto::class, $result);
        self::assertCount(1, $result->actions);
        self::assertSame('1', $result->actions[0]->getActionid());
        self::assertSame('Test Action', $result->actions[0]->getName());
    }

    public function testGetWithCustomOutput(): void
    {
        $dto = new GetActionDto(output: 'extend', filter: ['eventsource' => 0]);
        $expectedParams = ['output' => 'extend', 'filter' => ['eventsource' => 0]];
        $apiResult = [['actionid' => '1', 'name' => 'Test Action', 'eventsource' => 0, 'esc_period' => '1h']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::ACTION_GET, $expectedParams)
            ->willReturn($apiResult);

        $result = $this->action->get($dto);

        self::assertInstanceOf(GetActionResponseDto::class, $result);
        self::assertCount(1, $result->actions);
        self::assertSame('1', $result->actions[0]->getActionid());
        self::assertSame('Test Action', $result->actions[0]->getName());
    }

    public function testCreateValid(): void
    {
        $singleAction = new CreateSingleActionDto(
            name: 'Auto-notify Admin',
            eventsource: EventSourceEnum::TRIGGER,
            esc_period: '1h',
            operations: [['operationtype' => 0, 'opmessage' => ['default_msg' => 1], 'opmessage_grp' => [['usrgrpid' => '7']]]]
        );
        $dto = new CreateActionDto([$singleAction]);

        $expectedResult = ['actionids' => ['15']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::ACTION_CREATE, self::anything())
            ->willReturn($expectedResult);

        $result = $this->action->create($dto);

        self::assertSame(['15'], $result->actionids);
    }

    public function testUpdateValid(): void
    {
        $singleAction = new UpdateSingleActionDto(
            actionid: '15',
            status: StatusEnum::DISABLED
        );
        $dto = new UpdateActionDto([$singleAction]);

        $expectedResult = ['actionids' => ['15']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::ACTION_UPDATE, self::anything())
            ->willReturn($expectedResult);

        $result = $this->action->update($dto);

        self::assertSame(['15'], $result->actionids);
    }

    public function testDelete(): void
    {
        $dto = new DeleteActionDto(['17', '18']);

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::ACTION_DELETE, ['17', '18']);

        $this->action->delete($dto);

        self::assertTrue(true);
    }
}
