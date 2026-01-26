<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions;

use BytesCommerce\ZabbixApi\Actions\Dto\GetHistoryResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\PushHistoryDto;
use BytesCommerce\ZabbixApi\Actions\Dto\PushHistoryResponseDto;
use BytesCommerce\ZabbixApi\Enums\HistoryTypeEnum;
use BytesCommerce\ZabbixApi\Enums\OutputEnum;
use BytesCommerce\ZabbixApi\Enums\ZabbixAction;

final class History extends AbstractAction
{
    public static function getActionPrefix(): string
    {
        return 'history';
    }

    /**
     * @param list<string> $itemIds
     * @param array<string, mixed> $additionalParams
     */
    public function get(
        array $itemIds,
        HistoryTypeEnum $historyType = HistoryTypeEnum::NUMERIC_UNSIGNED,
        ?int $timeFrom = null,
        ?int $timeTill = null,
        ?int $limit = null,
        string $sortField = 'clock',
        string $sortOrder = 'DESC',
        bool $preserveKeys = false,
        array $additionalParams = [],
    ): GetHistoryResponseDto {
        if ($itemIds === []) {
            return new GetHistoryResponseDto([]);
        }

        $params = [
            'output' => OutputEnum::EXTEND->value,
            'history' => $historyType->value,
            'itemids' => $itemIds,
            'sortfield' => $sortField,
            'sortorder' => $sortOrder,
            'preservekeys' => $preserveKeys,
            ...$additionalParams,
        ];

        if ($timeFrom !== null) {
            $params['time_from'] = $timeFrom;
        }

        if ($timeTill !== null) {
            $params['time_till'] = $timeTill;
        }

        if ($limit !== null) {
            $params['limit'] = $limit;
        }

        $result = $this->client->call(ZabbixAction::HISTORY_GET, $params);

        return GetHistoryResponseDto::fromArray(is_array($result) ? $result : []);
    }

    /**
     * @param list<string> $itemIds
     */
    public function getLast24Hours(
        array $itemIds,
        HistoryTypeEnum $historyType = HistoryTypeEnum::NUMERIC_UNSIGNED,
        ?int $limit = null,
    ): GetHistoryResponseDto {
        $now = time();
        $twentyFourHoursAgo = $now - 86400;

        return $this->get(
            itemIds: $itemIds,
            historyType: $historyType,
            timeFrom: $twentyFourHoursAgo,
            timeTill: $now,
            limit: $limit,
        );
    }

    /**
     * @param list<string> $itemIds
     */
    public function getLatest(
        array $itemIds,
        HistoryTypeEnum $historyType = HistoryTypeEnum::NUMERIC_UNSIGNED,
        int $limit = 10,
    ): GetHistoryResponseDto {
        return $this->get(
            itemIds: $itemIds,
            historyType: $historyType,
            limit: $limit,
        );
    }

    /**
     * @param list<string> $itemIds
     */
    public function count(
        array $itemIds,
        HistoryTypeEnum $historyType = HistoryTypeEnum::NUMERIC_UNSIGNED,
        ?int $timeFrom = null,
        ?int $timeTill = null,
    ): int {
        if ($itemIds === []) {
            return 0;
        }

        $params = [
            'countOutput' => true,
            'history' => $historyType->value,
            'itemids' => $itemIds,
        ];

        if ($timeFrom !== null) {
            $params['time_from'] = $timeFrom;
        }

        if ($timeTill !== null) {
            $params['time_till'] = $timeTill;
        }

        $result = $this->client->call(ZabbixAction::HISTORY_GET, $params);

        return is_numeric($result) ? (int) $result : 0;
    }

    /**
     * @param list<string> $itemIds
     * @param array<string, mixed> $filter
     */
    public function getWithFilter(
        array $itemIds,
        array $filter,
        HistoryTypeEnum $historyType = HistoryTypeEnum::NUMERIC_UNSIGNED,
        ?int $timeFrom = null,
        ?int $timeTill = null,
        ?int $limit = null,
    ): GetHistoryResponseDto {
        if ($itemIds === []) {
            return new GetHistoryResponseDto([]);
        }

        $params = [
            'output' => OutputEnum::EXTEND->value,
            'history' => $historyType->value,
            'itemids' => $itemIds,
            'filter' => $filter,
            'sortfield' => 'clock',
            'sortorder' => 'DESC',
        ];

        if ($timeFrom !== null) {
            $params['time_from'] = $timeFrom;
        }

        if ($timeTill !== null) {
            $params['time_till'] = $timeTill;
        }

        if ($limit !== null) {
            $params['limit'] = $limit;
        }

        $result = $this->client->call(ZabbixAction::HISTORY_GET, $params);

        return GetHistoryResponseDto::fromArray(is_array($result) ? $result : []);
    }

    /**
     * @param list<string> $itemIds
     * @param array<string, mixed> $search
     */
    public function search(
        array $itemIds,
        array $search,
        HistoryTypeEnum $historyType = HistoryTypeEnum::NUMERIC_UNSIGNED,
        ?int $timeFrom = null,
        ?int $timeTill = null,
        ?int $limit = null,
    ): GetHistoryResponseDto {
        if ($itemIds === []) {
            return new GetHistoryResponseDto([]);
        }

        $params = [
            'output' => OutputEnum::EXTEND->value,
            'history' => $historyType->value,
            'itemids' => $itemIds,
            'search' => $search,
            'searchWildcardsEnabled' => true,
            'sortfield' => 'clock',
            'sortorder' => 'DESC',
        ];

        if ($timeFrom !== null) {
            $params['time_from'] = $timeFrom;
        }

        if ($timeTill !== null) {
            $params['time_till'] = $timeTill;
        }

        if ($limit !== null) {
            $params['limit'] = $limit;
        }

        $result = $this->client->call(ZabbixAction::HISTORY_GET, $params);

        return GetHistoryResponseDto::fromArray(is_array($result) ? $result : []);
    }

    /**
     * @param list<string> $itemIds
     * @param list<string>|string $output
     */
    public function getWithCustomOutput(
        array $itemIds,
        array|string $output,
        HistoryTypeEnum $historyType = HistoryTypeEnum::NUMERIC_UNSIGNED,
        ?int $timeFrom = null,
        ?int $timeTill = null,
        ?int $limit = null,
    ): GetHistoryResponseDto {
        if ($itemIds === []) {
            return new GetHistoryResponseDto([]);
        }

        $params = [
            'output' => $output,
            'history' => $historyType->value,
            'itemids' => $itemIds,
            'sortfield' => 'clock',
            'sortorder' => 'DESC',
        ];

        if ($timeFrom !== null) {
            $params['time_from'] = $timeFrom;
        }

        if ($timeTill !== null) {
            $params['time_till'] = $timeTill;
        }

        if ($limit !== null) {
            $params['limit'] = $limit;
        }

        $result = $this->client->call(ZabbixAction::HISTORY_GET, $params);

        return GetHistoryResponseDto::fromArray(is_array($result) ? $result : []);
    }

    /**
     * @param list<PushHistoryDto> $historyData
     */
    public function push(array $historyData): PushHistoryResponseDto
    {
        if ($historyData === []) {
            return new PushHistoryResponseDto([], '');
        }

        $params = array_map(
            static fn (PushHistoryDto $dto) => $dto->toArray(),
            $historyData,
        );

        $result = $this->client->call(ZabbixAction::HISTORY_PUSH, $params);

        return PushHistoryResponseDto::fromArray(is_array($result) ? $result : []);
    }
}
