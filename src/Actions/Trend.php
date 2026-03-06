<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions;

use BytesCommerce\ZabbixApi\Actions\Dto\GetTrendResponseDto;
use BytesCommerce\ZabbixApi\Enums\OutputEnum;
use BytesCommerce\ZabbixApi\Enums\ZabbixAction;

final class Trend extends AbstractAction
{
    public static function getActionPrefix(): string
    {
        return 'trend';
    }

    /**
     * @param list<string> $itemIds
     * @param array<string, mixed> $additionalParams
     */
    public function get(
        array $itemIds,
        ?int $timeFrom = null,
        ?int $timeTill = null,
        ?int $limit = null,
        string $sortField = 'clock',
        string $sortOrder = 'DESC',
        bool $preserveKeys = false,
        array $additionalParams = [],
    ): GetTrendResponseDto {
        if ($itemIds === []) {
            return new GetTrendResponseDto([]);
        }

        $params = [
            'output' => OutputEnum::EXTEND->value,
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

        $result = $this->client->call(ZabbixAction::TREND_GET, $params);

        /** @var array<int, array<string, mixed>> $trendData */
        $trendData = is_array($result) ? $result : [];

        return GetTrendResponseDto::fromArray($trendData);
    }

    /**
     * @param list<string> $itemIds
     */
    public function getLast24Hours(
        array $itemIds,
        ?int $limit = null,
    ): GetTrendResponseDto {
        $now = time();
        $twentyFourHoursAgo = $now - 86400;

        return $this->get(
            itemIds: $itemIds,
            timeFrom: $twentyFourHoursAgo,
            timeTill: $now,
            limit: $limit,
        );
    }

    /**
     * @param list<string> $itemIds
     */
    public function getLast7Days(
        array $itemIds,
        ?int $limit = null,
    ): GetTrendResponseDto {
        $now = time();
        $sevenDaysAgo = $now - (7 * 86400);

        return $this->get(
            itemIds: $itemIds,
            timeFrom: $sevenDaysAgo,
            timeTill: $now,
            limit: $limit,
        );
    }

    /**
     * @param list<string> $itemIds
     */
    public function getLast30Days(
        array $itemIds,
        ?int $limit = null,
    ): GetTrendResponseDto {
        $now = time();
        $thirtyDaysAgo = $now - (30 * 86400);

        return $this->get(
            itemIds: $itemIds,
            timeFrom: $thirtyDaysAgo,
            timeTill: $now,
            limit: $limit,
        );
    }

    /**
     * @param list<string> $itemIds
     * @param array<string, mixed> $filter
     */
    public function getWithFilter(
        array $itemIds,
        array $filter,
        ?int $timeFrom = null,
        ?int $timeTill = null,
        ?int $limit = null,
    ): GetTrendResponseDto {
        if ($itemIds === []) {
            return new GetTrendResponseDto([]);
        }

        $params = [
            'output' => OutputEnum::EXTEND->value,
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

        $result = $this->client->call(ZabbixAction::TREND_GET, $params);

        /** @var array<int, array<string, mixed>> $trendData */
        $trendData = is_array($result) ? $result : [];

        return GetTrendResponseDto::fromArray($trendData);
    }

    /**
     * @param list<string> $itemIds
     */
    public function count(
        array $itemIds,
        ?int $timeFrom = null,
        ?int $timeTill = null,
    ): int {
        if ($itemIds === []) {
            return 0;
        }

        $params = [
            'countOutput' => true,
            'itemids' => $itemIds,
        ];

        if ($timeFrom !== null) {
            $params['time_from'] = $timeFrom;
        }

        if ($timeTill !== null) {
            $params['time_till'] = $timeTill;
        }

        $result = $this->client->call(ZabbixAction::TREND_GET, $params);

        return is_numeric($result) ? (int) $result : 0;
    }
}