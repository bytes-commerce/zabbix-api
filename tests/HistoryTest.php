<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Tests;

use BytesCommerce\ZabbixApi\Actions\Dto\GetHistoryResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\HistoryDto;
use BytesCommerce\ZabbixApi\Actions\Dto\PushHistoryDto;
use BytesCommerce\ZabbixApi\Actions\Dto\PushHistoryResponseDto;
use BytesCommerce\ZabbixApi\Actions\History;
use BytesCommerce\ZabbixApi\Enums\HistoryTypeEnum;
use BytesCommerce\ZabbixApi\Enums\ZabbixAction;
use BytesCommerce\ZabbixApi\ZabbixClientInterface;
use PHPUnit\Framework\TestCase;

final class HistoryTest extends TestCase
{
    private ZabbixClientInterface $zabbixClient;

    private History $history;

    protected function setUp(): void
    {
        $this->zabbixClient = $this->createMock(ZabbixClientInterface::class);
        $this->history = new History($this->zabbixClient);
    }

    public function testGetWithDefaultParameters(): void
    {
        $itemIds = ['12345', '67890'];
        $expectedParams = [
            'output' => 'extend',
            'history' => 3,
            'itemids' => $itemIds,
            'sortfield' => 'clock',
            'sortorder' => 'DESC',
            'preservekeys' => false,
        ];
        $expectedResult = [
            [
                'itemid' => '12345',
                'clock' => '1706227200',
                'value' => '42.5',
                'ns' => '123456789',
            ],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HISTORY_GET, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->history->get($itemIds);

        self::assertInstanceOf(GetHistoryResponseDto::class, $result);
        self::assertCount(1, $result->getHistory());
    }

    public function testGetWithEmptyItemIds(): void
    {
        $result = $this->history->get([]);

        self::assertInstanceOf(GetHistoryResponseDto::class, $result);
        self::assertCount(0, $result->getHistory());
    }

    public function testGetWithTimeRange(): void
    {
        $itemIds = ['12345'];
        $timeFrom = 1706227200;
        $timeTill = 1706313600;
        $expectedParams = [
            'output' => 'extend',
            'history' => 3,
            'itemids' => $itemIds,
            'sortfield' => 'clock',
            'sortorder' => 'DESC',
            'preservekeys' => false,
            'time_from' => $timeFrom,
            'time_till' => $timeTill,
        ];
        $expectedResult = [
            [
                'itemid' => '12345',
                'clock' => '1706227200',
                'value' => '42.5',
            ],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HISTORY_GET, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->history->get($itemIds, HistoryTypeEnum::NUMERIC_UNSIGNED, $timeFrom, $timeTill);

        self::assertInstanceOf(GetHistoryResponseDto::class, $result);
    }

    public function testGetWithLimit(): void
    {
        $itemIds = ['12345'];
        $limit = 50;
        $expectedParams = [
            'output' => 'extend',
            'history' => 3,
            'itemids' => $itemIds,
            'sortfield' => 'clock',
            'sortorder' => 'DESC',
            'preservekeys' => false,
            'limit' => $limit,
        ];
        $expectedResult = [
            [
                'itemid' => '12345',
                'clock' => '1706227200',
                'value' => '42.5',
            ],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HISTORY_GET, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->history->get($itemIds, HistoryTypeEnum::NUMERIC_UNSIGNED, null, null, $limit);

        self::assertInstanceOf(GetHistoryResponseDto::class, $result);
    }

    public function testGetWithDifferentHistoryType(): void
    {
        $itemIds = ['12345'];
        $expectedParams = [
            'output' => 'extend',
            'history' => 2,
            'itemids' => $itemIds,
            'sortfield' => 'clock',
            'sortorder' => 'DESC',
            'preservekeys' => false,
        ];
        $expectedResult = [
            [
                'itemid' => '12345',
                'clock' => '1706227200',
                'value' => 'Log message',
                'timestamp' => '1706227200',
                'severity' => '3',
                'source' => 'application',
            ],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HISTORY_GET, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->history->get($itemIds, HistoryTypeEnum::LOG);

        self::assertInstanceOf(GetHistoryResponseDto::class, $result);
    }

    public function testGetWithPreserveKeys(): void
    {
        $itemIds = ['12345'];
        $expectedParams = [
            'output' => 'extend',
            'history' => 3,
            'itemids' => $itemIds,
            'sortfield' => 'clock',
            'sortorder' => 'DESC',
            'preservekeys' => true,
        ];
        $expectedResult = [
            [
                'itemid' => '12345',
                'clock' => '1706227200',
                'value' => '42.5',
            ],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HISTORY_GET, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->history->get($itemIds, HistoryTypeEnum::NUMERIC_UNSIGNED, null, null, null, 'clock', 'DESC', true);

        self::assertInstanceOf(GetHistoryResponseDto::class, $result);
    }

    public function testGetLast24Hours(): void
    {
        $itemIds = ['12345'];
        $expectedResult = [
            [
                'itemid' => '12345',
                'clock' => '1706227200',
                'value' => '42.5',
            ],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(
                ZabbixAction::HISTORY_GET,
                self::callback(function (array $params) {
                    return isset($params['time_from']) && isset($params['time_till']);
                })
            )
            ->willReturn($expectedResult);

        $result = $this->history->getLast24Hours($itemIds);

        self::assertInstanceOf(GetHistoryResponseDto::class, $result);
    }

    public function testGetLatest(): void
    {
        $itemIds = ['12345'];
        $limit = 10;
        $expectedParams = [
            'output' => 'extend',
            'history' => 3,
            'itemids' => $itemIds,
            'sortfield' => 'clock',
            'sortorder' => 'DESC',
            'preservekeys' => false,
            'limit' => $limit,
        ];
        $expectedResult = [
            [
                'itemid' => '12345',
                'clock' => '1706227200',
                'value' => '42.5',
            ],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HISTORY_GET, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->history->getLatest($itemIds, HistoryTypeEnum::NUMERIC_UNSIGNED, $limit);

        self::assertInstanceOf(GetHistoryResponseDto::class, $result);
    }

    public function testCount(): void
    {
        $itemIds = ['12345', '67890'];
        $expectedParams = [
            'countOutput' => true,
            'history' => 3,
            'itemids' => $itemIds,
        ];
        $expectedResult = 150;

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HISTORY_GET, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->history->count($itemIds);

        self::assertSame(150, $result);
    }

    public function testCountWithEmptyItemIds(): void
    {
        $result = $this->history->count([]);

        self::assertSame(0, $result);
    }

    public function testCountWithTimeRange(): void
    {
        $itemIds = ['12345'];
        $timeFrom = 1706227200;
        $timeTill = 1706313600;
        $expectedParams = [
            'countOutput' => true,
            'history' => 3,
            'itemids' => $itemIds,
            'time_from' => $timeFrom,
            'time_till' => $timeTill,
        ];
        $expectedResult = 50;

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HISTORY_GET, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->history->count($itemIds, HistoryTypeEnum::NUMERIC_UNSIGNED, $timeFrom, $timeTill);

        self::assertSame(50, $result);
    }

    public function testGetWithFilter(): void
    {
        $itemIds = ['12345'];
        $filter = ['value' => '42.5'];
        $expectedParams = [
            'output' => 'extend',
            'history' => 3,
            'itemids' => $itemIds,
            'filter' => $filter,
            'sortfield' => 'clock',
            'sortorder' => 'DESC',
        ];
        $expectedResult = [
            [
                'itemid' => '12345',
                'clock' => '1706227200',
                'value' => '42.5',
            ],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HISTORY_GET, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->history->getWithFilter($itemIds, $filter);

        self::assertInstanceOf(GetHistoryResponseDto::class, $result);
    }

    public function testGetWithFilterEmptyItemIds(): void
    {
        $result = $this->history->getWithFilter([], ['value' => '42.5']);

        self::assertInstanceOf(GetHistoryResponseDto::class, $result);
        self::assertCount(0, $result->getHistory());
    }

    public function testSearch(): void
    {
        $itemIds = ['12345'];
        $search = ['value' => '42'];
        $expectedParams = [
            'output' => 'extend',
            'history' => 3,
            'itemids' => $itemIds,
            'search' => $search,
            'searchWildcardsEnabled' => true,
            'sortfield' => 'clock',
            'sortorder' => 'DESC',
        ];
        $expectedResult = [
            [
                'itemid' => '12345',
                'clock' => '1706227200',
                'value' => '42.5',
            ],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HISTORY_GET, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->history->search($itemIds, $search);

        self::assertInstanceOf(GetHistoryResponseDto::class, $result);
    }

    public function testSearchEmptyItemIds(): void
    {
        $result = $this->history->search([], ['value' => '42']);

        self::assertInstanceOf(GetHistoryResponseDto::class, $result);
        self::assertCount(0, $result->getHistory());
    }

    public function testGetWithCustomOutput(): void
    {
        $itemIds = ['12345'];
        $output = ['itemid', 'clock', 'value'];
        $expectedParams = [
            'output' => $output,
            'history' => 3,
            'itemids' => $itemIds,
            'sortfield' => 'clock',
            'sortorder' => 'DESC',
        ];
        $expectedResult = [
            [
                'itemid' => '12345',
                'clock' => '1706227200',
                'value' => '42.5',
            ],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HISTORY_GET, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->history->getWithCustomOutput($itemIds, $output);

        self::assertInstanceOf(GetHistoryResponseDto::class, $result);
    }

    public function testGetWithCustomOutputEmptyItemIds(): void
    {
        $result = $this->history->getWithCustomOutput([], ['itemid', 'clock']);

        self::assertInstanceOf(GetHistoryResponseDto::class, $result);
        self::assertCount(0, $result->getHistory());
    }

    public function testHistoryDtoFromArray(): void
    {
        $data = [
            'itemid' => '12345',
            'clock' => '1706227200',
            'value' => '42.5',
            'ns' => '123456789',
            'timestamp' => '1706227200',
            'logeventid' => '1',
            'severity' => '3',
            'source' => 'application',
            'eventid' => '67890',
        ];

        $dto = HistoryDto::fromArray($data);

        self::assertSame('12345', $dto->getItemid());
        self::assertSame(1706227200, $dto->getClock());
        self::assertSame('42.5', $dto->getValue());
        self::assertSame(123456789, $dto->getNs());
        self::assertSame(1706227200, $dto->getTimestamp());
        self::assertSame(1, $dto->getLogeventid());
        self::assertSame(3, $dto->getSeverity());
        self::assertSame('application', $dto->getSource());
        self::assertSame('67890', $dto->getEventid());
    }

    public function testHistoryDtoFromArrayWithOptionalFields(): void
    {
        $data = [
            'itemid' => '12345',
            'clock' => '1706227200',
            'value' => '42.5',
        ];

        $dto = HistoryDto::fromArray($data);

        self::assertSame('12345', $dto->getItemid());
        self::assertSame(1706227200, $dto->getClock());
        self::assertSame('42.5', $dto->getValue());
        self::assertNull($dto->getNs());
        self::assertNull($dto->getTimestamp());
        self::assertNull($dto->getLogeventid());
        self::assertNull($dto->getSeverity());
        self::assertNull($dto->getSource());
        self::assertNull($dto->getEventid());
    }

    public function testGetHistoryResponseDtoFromArray(): void
    {
        $data = [
            [
                'itemid' => '12345',
                'clock' => '1706227200',
                'value' => '42.5',
            ],
            [
                'itemid' => '67890',
                'clock' => '1706227300',
                'value' => '43.0',
            ],
        ];

        $dto = GetHistoryResponseDto::fromArray($data);

        self::assertCount(2, $dto->getHistory());
        self::assertSame('12345', $dto->getHistory()[0]->getItemid());
        self::assertSame('67890', $dto->getHistory()[1]->getItemid());
    }

    public function testGetHistoryResponseDtoFromEmptyArray(): void
    {
        $dto = GetHistoryResponseDto::fromArray([]);

        self::assertCount(0, $dto->getHistory());
    }

    public function testPushWithSingleHistoryData(): void
    {
        $historyData = [
            new PushHistoryDto(
                itemid: '12345',
                clock: 1706227200,
                value: '42.5',
            ),
        ];
        $expectedParams = [
            [
                'itemid' => '12345',
                'clock' => 1706227200,
                'value' => '42.5',
            ],
        ];
        $expectedResult = [
            'historyids' => ['67890'],
            'response' => 'success',
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HISTORY_PUSH, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->history->push($historyData);

        self::assertInstanceOf(PushHistoryResponseDto::class, $result);
        self::assertSame(['67890'], $result->getHistoryids());
        self::assertSame('success', $result->getResponse());
        self::assertTrue($result->isSuccess());
    }

    public function testPushWithMultipleHistoryData(): void
    {
        $historyData = [
            new PushHistoryDto(
                itemid: '12345',
                clock: 1706227200,
                value: '42.5',
            ),
            new PushHistoryDto(
                itemid: '67890',
                clock: 1706227300,
                value: '43.0',
            ),
        ];
        $expectedParams = [
            [
                'itemid' => '12345',
                'clock' => 1706227200,
                'value' => '42.5',
            ],
            [
                'itemid' => '67890',
                'clock' => 1706227300,
                'value' => '43.0',
            ],
        ];
        $expectedResult = [
            'historyids' => ['11111', '22222'],
            'response' => 'success',
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HISTORY_PUSH, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->history->push($historyData);

        self::assertInstanceOf(PushHistoryResponseDto::class, $result);
        self::assertSame(['11111', '22222'], $result->getHistoryids());
        self::assertSame('success', $result->getResponse());
        self::assertTrue($result->isSuccess());
    }

    public function testPushWithEmptyHistoryData(): void
    {
        $result = $this->history->push([]);

        self::assertInstanceOf(PushHistoryResponseDto::class, $result);
        self::assertSame([], $result->getHistoryids());
        self::assertSame('', $result->getResponse());
        self::assertFalse($result->isSuccess());
    }

    public function testPushWithOptionalFields(): void
    {
        $historyData = [
            new PushHistoryDto(
                itemid: '12345',
                clock: 1706227200,
                value: 'Log message',
                ns: 123456789,
                timestamp: 1706227200,
                logeventid: 1,
                severity: 3,
                source: 'application',
                eventid: '67890',
            ),
        ];
        $expectedParams = [
            [
                'itemid' => '12345',
                'clock' => 1706227200,
                'value' => 'Log message',
                'ns' => 123456789,
                'timestamp' => 1706227200,
                'logeventid' => 1,
                'severity' => 3,
                'source' => 'application',
                'eventid' => '67890',
            ],
        ];
        $expectedResult = [
            'historyids' => ['11111'],
            'response' => 'success',
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HISTORY_PUSH, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->history->push($historyData);

        self::assertInstanceOf(PushHistoryResponseDto::class, $result);
        self::assertSame(['11111'], $result->getHistoryids());
        self::assertSame('success', $result->getResponse());
        self::assertTrue($result->isSuccess());
    }

    public function testPushWithErrorResponse(): void
    {
        $historyData = [
            new PushHistoryDto(
                itemid: '12345',
                clock: 1706227200,
                value: '42.5',
            ),
        ];
        $expectedResult = [
            'historyids' => [],
            'response' => 'error',
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->willReturn($expectedResult);

        $result = $this->history->push($historyData);

        self::assertInstanceOf(PushHistoryResponseDto::class, $result);
        self::assertSame([], $result->getHistoryids());
        self::assertSame('error', $result->getResponse());
        self::assertFalse($result->isSuccess());
    }

    public function testPushHistoryDtoToArray(): void
    {
        $dto = new PushHistoryDto(
            itemid: '12345',
            clock: 1706227200,
            value: '42.5',
            ns: 123456789,
            timestamp: 1706227200,
            logeventid: 1,
            severity: 3,
            source: 'application',
            eventid: '67890',
        );

        $expected = [
            'itemid' => '12345',
            'clock' => 1706227200,
            'value' => '42.5',
            'ns' => 123456789,
            'timestamp' => 1706227200,
            'logeventid' => 1,
            'severity' => 3,
            'source' => 'application',
            'eventid' => '67890',
        ];

        self::assertSame($expected, $dto->toArray());
    }

    public function testPushHistoryDtoToArrayWithOnlyRequiredFields(): void
    {
        $dto = new PushHistoryDto(
            itemid: '12345',
            clock: 1706227200,
            value: '42.5',
        );

        $expected = [
            'itemid' => '12345',
            'clock' => 1706227200,
            'value' => '42.5',
        ];

        self::assertSame($expected, $dto->toArray());
    }

    public function testPushHistoryResponseDtoFromArray(): void
    {
        $data = [
            'historyids' => ['11111', '22222'],
            'response' => 'success',
        ];

        $dto = PushHistoryResponseDto::fromArray($data);

        self::assertSame(['11111', '22222'], $dto->getHistoryids());
        self::assertSame('success', $dto->getResponse());
        self::assertTrue($dto->isSuccess());
    }

    public function testPushHistoryResponseDtoFromArrayWithEmptyHistoryids(): void
    {
        $data = [
            'historyids' => [],
            'response' => 'error',
        ];

        $dto = PushHistoryResponseDto::fromArray($data);

        self::assertSame([], $dto->getHistoryids());
        self::assertSame('error', $dto->getResponse());
        self::assertFalse($dto->isSuccess());
    }

    public function testPushHistoryResponseDtoFromArrayWithMissingFields(): void
    {
        $data = [];

        $dto = PushHistoryResponseDto::fromArray($data);

        self::assertSame([], $dto->getHistoryids());
        self::assertSame('', $dto->getResponse());
        self::assertFalse($dto->isSuccess());
    }

    public function testPushHistoryDtoGetters(): void
    {
        $dto = new PushHistoryDto(
            itemid: '12345',
            clock: 1706227200,
            value: '42.5',
            ns: 123456789,
            timestamp: 1706227200,
            logeventid: 1,
            severity: 3,
            source: 'application',
            eventid: '67890',
        );

        self::assertSame('12345', $dto->getItemid());
        self::assertSame(1706227200, $dto->getClock());
        self::assertSame('42.5', $dto->getValue());
        self::assertSame(123456789, $dto->getNs());
        self::assertSame(1706227200, $dto->getTimestamp());
        self::assertSame(1, $dto->getLogeventid());
        self::assertSame(3, $dto->getSeverity());
        self::assertSame('application', $dto->getSource());
        self::assertSame('67890', $dto->getEventid());
    }

    public function testPushHistoryDtoGettersWithNullOptionalFields(): void
    {
        $dto = new PushHistoryDto(
            itemid: '12345',
            clock: 1706227200,
            value: '42.5',
        );

        self::assertSame('12345', $dto->getItemid());
        self::assertSame(1706227200, $dto->getClock());
        self::assertSame('42.5', $dto->getValue());
        self::assertNull($dto->getNs());
        self::assertNull($dto->getTimestamp());
        self::assertNull($dto->getLogeventid());
        self::assertNull($dto->getSeverity());
        self::assertNull($dto->getSource());
        self::assertNull($dto->getEventid());
    }
}
