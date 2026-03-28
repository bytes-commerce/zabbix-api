<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Tests\Actions;

use BytesCommerce\ZabbixApi\Actions\Dto\GetTrendResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\TrendItemDto;
use BytesCommerce\ZabbixApi\Actions\Trend;
use BytesCommerce\ZabbixApi\Enums\ZabbixAction;
use BytesCommerce\ZabbixApi\ZabbixClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class TrendTest extends TestCase
{
    private ZabbixClientInterface&MockObject $client;

    private Trend $trend;

    protected function setUp(): void
    {
        $this->client = $this->createMock(ZabbixClientInterface::class);
        $this->trend = new Trend($this->client);
    }

    public function testGetActionPrefix(): void
    {
        $this->assertSame('trend', Trend::getActionPrefix());
    }

    public function testGetReturnsEmptyResponseForEmptyItemIds(): void
    {
        $result = $this->trend->get([]);

        $this->assertTrue($result->isEmpty());
        $this->assertCount(0, $result->items);
    }

    public function testGetCallsTrendGetAction(): void
    {
        $this->client->expects($this->once())
            ->method('call')
            ->with(
                ZabbixAction::TREND_GET,
                $this->callback(static fn (array $params) => $params['itemids'] === ['123', '456'])
            )
            ->willReturn([
                [
                    'itemid' => '123',
                    'clock' => 1709500000,
                    'num' => 60,
                    'value_min' => '10.5',
                    'value_avg' => '25.3',
                    'value_max' => '50.0',
                ],
            ]);

        $result = $this->trend->get(['123', '456']);

        $this->assertCount(1, $result->items);
        $this->assertSame('123', $result->items[0]->itemid);
        $this->assertSame(1709500000, $result->items[0]->clock);
        $this->assertSame(60, $result->items[0]->num);
        $this->assertSame(10.5, $result->items[0]->valueMin);
        $this->assertSame(25.3, $result->items[0]->valueAvg);
        $this->assertSame(50.0, $result->items[0]->valueMax);
    }

    public function testGetWithTimeRange(): void
    {
        $timeFrom = 1709400000;
        $timeTill = 1709500000;

        $this->client->expects($this->once())
            ->method('call')
            ->with(
                ZabbixAction::TREND_GET,
                $this->callback(static fn (array $params) => 
                    $params['time_from'] === $timeFrom 
                    && $params['time_till'] === $timeTill
                )
            )
            ->willReturn([]);

        $result = $this->trend->get(['123'], timeFrom: $timeFrom, timeTill: $timeTill);

        $this->assertTrue($result->isEmpty());
    }

    public function testGetLast24Hours(): void
    {
        $this->client->expects($this->once())
            ->method('call')
            ->with(
                ZabbixAction::TREND_GET,
                $this->callback(static fn (array $params) => 
                    isset($params['time_from']) 
                    && isset($params['time_till'])
                    && $params['time_till'] - $params['time_from'] === 86400
                )
            )
            ->willReturn([]);

        $result = $this->trend->getLast24Hours(['123']);

        $this->assertTrue($result->isEmpty());
    }

    public function testGetLast7Days(): void
    {
        $this->client->expects($this->once())
            ->method('call')
            ->with(
                ZabbixAction::TREND_GET,
                $this->callback(static fn (array $params) => 
                    isset($params['time_from']) 
                    && isset($params['time_till'])
                    && $params['time_till'] - $params['time_from'] === 7 * 86400
                )
            )
            ->willReturn([]);

        $result = $this->trend->getLast7Days(['123']);

        $this->assertTrue($result->isEmpty());
    }

    public function testGetLast30Days(): void
    {
        $this->client->expects($this->once())
            ->method('call')
            ->with(
                ZabbixAction::TREND_GET,
                $this->callback(static fn (array $params) => 
                    isset($params['time_from']) 
                    && isset($params['time_till'])
                    && $params['time_till'] - $params['time_from'] === 30 * 86400
                )
            )
            ->willReturn([]);

        $result = $this->trend->getLast30Days(['123']);

        $this->assertTrue($result->isEmpty());
    }

    public function testCountReturnsZeroForEmptyItemIds(): void
    {
        $result = $this->trend->count([]);

        $this->assertSame(0, $result);
    }

    public function testCountReturnsCountFromResponse(): void
    {
        $this->client->expects($this->once())
            ->method('call')
            ->with(
                ZabbixAction::TREND_GET,
                $this->callback(static fn (array $params) => $params['countOutput'] === true)
            )
            ->willReturn(42);

        $result = $this->trend->count(['123']);

        $this->assertSame(42, $result);
    }

    public function testCountReturnsZeroForNonNumericResponse(): void
    {
        $this->client->expects($this->once())
            ->method('call')
            ->willReturn(null);

        $result = $this->trend->count(['123']);

        $this->assertSame(0, $result);
    }
}