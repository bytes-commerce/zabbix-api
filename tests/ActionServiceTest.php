<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Tests;

use BytesCommerce\Zabbix\Actions\Item;
use BytesCommerce\Zabbix\ActionService;
use BytesCommerce\Zabbix\Enums\ZabbixAction;
use BytesCommerce\Zabbix\ZabbixApiException;
use BytesCommerce\Zabbix\ZabbixClientInterface;
use PHPUnit\Framework\TestCase;

final class ActionServiceTest extends TestCase
{
    private ZabbixClientInterface $zabbixClient;

    private ActionService $actionService;

    protected function setUp(): void
    {
        $this->zabbixClient = $this->createMock(ZabbixClientInterface::class);
        $this->actionService = new ActionService($this->zabbixClient);
    }

    public function testCallWithDefaultMethod(): void
    {
        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::ITEM_GET, ['a' => 1])
            ->willReturn(['result' => 'data']);

        $result = $this->actionService->call(Item::class, ['a' => 1]);

        self::assertSame(['result' => 'data'], $result);
    }

    public function testCallWithExplicitMethod(): void
    {
        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::ITEM_CREATE, ['name' => 'test'])
            ->willReturn(['itemid' => 1]);

        $result = $this->actionService->call(Item::class, ['method' => 'create', 'params' => ['name' => 'test']]);

        self::assertSame(['itemid' => 1], $result);
    }

    public function testCallUnsupportedClass(): void
    {
        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Unsupported action class: stdClass');

        $this->actionService->call(\stdClass::class, []);
    }

    public function testCallInvalidMethod(): void
    {
        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Invalid action method: item.invalid');

        $this->actionService->call(Item::class, ['method' => 'invalid']);
    }
}
