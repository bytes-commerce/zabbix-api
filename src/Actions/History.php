<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions;

use BytesCommerce\ZabbixApi\Enums\HistoryTypeEnum;
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
     *
     * @return list<array<string, mixed>>
     */
    public function get(
        array $itemIds,
        HistoryTypeEnum $historyType = HistoryTypeEnum::NUMERIC_UNSIGNED,
        ?int $timeFrom = null,
        ?int $timeTill = null,
        int $limit = 100,
        string $sortField = 'clock',
        string $sortOrder = 'DESC',
        array $additionalParams = [],
    ): array {
        if ($itemIds === []) {
            return [];
        }

        $params = [
            'output' => 'extend',
            'history' => $historyType->value,
            'itemids' => $itemIds,
            'sortfield' => $sortField,
            'sortorder' => $sortOrder,
            'limit' => $limit,
            ...$additionalParams,
        ];

        if ($timeFrom !== null) {
            $params['time_from'] = $timeFrom;
        }

        if ($timeTill !== null) {
            $params['time_till'] = $timeTill;
        }

        $result = $this->client->call(ZabbixAction::HISTORY_GET, $params);

        return is_array($result) ? $result : [];
    }

    /**
     * @param list<string> $itemIds
     *
     * @return list<array<string, mixed>>
     */
    public function getLast24Hours(
        array $itemIds,
        HistoryTypeEnum $historyType = HistoryTypeEnum::NUMERIC_UNSIGNED,
        int $limit = 100,
    ): array {
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
     *
     * @return list<array<string, mixed>>
     */
    public function getLatest(
        array $itemIds,
        HistoryTypeEnum $historyType = HistoryTypeEnum::NUMERIC_UNSIGNED,
        int $limit = 10,
    ): array {
        return $this->get(
            itemIds: $itemIds,
            historyType: $historyType,
            limit: $limit,
        );
    }
}
