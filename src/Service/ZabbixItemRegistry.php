<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Service;

use BytesCommerce\ZabbixApi\Contract\ItemDefinitionProviderInterface;
use BytesCommerce\ZabbixApi\Contract\ZabbixNamingProviderInterface;
use Psr\Cache\CacheItemPoolInterface;

final readonly class ZabbixItemRegistry implements ItemDefinitionProviderInterface
{
    private const string CACHE_KEY_ITEM_IDS = 'zabbix.item_ids';

    private const string CACHE_KEY_HOST_ID = 'zabbix.host_id';

    public function __construct(
        private CacheItemPoolInterface $cache,
        private ZabbixNamingProviderInterface $naming,
    ) {
    }

    public function getHostId(): ?string
    {
        $item = $this->cache->getItem(self::CACHE_KEY_HOST_ID);
        $hostId = $item->get();

        return $hostId !== null ? (string) $hostId : null;
    }

    public function setHostId(string $hostId): void
    {
        $item = $this->cache->getItem(self::CACHE_KEY_HOST_ID);
        $item->set($hostId);
        $this->cache->save($item);
    }

    public function getItemIdForKey(string $key): ?string
    {
        $item = $this->cache->getItem(self::CACHE_KEY_ITEM_IDS);
        $itemIds = $item->get();
        if (!\is_array($itemIds)) {
            return null;
        }

        return $itemIds[$key] ?? null;
    }

    public function setItemId(string $key, string $itemId): void
    {
        $item = $this->cache->getItem(self::CACHE_KEY_ITEM_IDS);
        $itemIds = $item->get();
        if (!\is_array($itemIds)) {
            $itemIds = [];
        }
        $itemIds[$key] = $itemId;
        $item->set($itemIds);
        $this->cache->save($item);
    }

    public function getAllItemDefinitions(): array
    {
        return [
            'tx.duration_ms' => [
                'name' => 'Transaction Duration (ms)',
                'type' => 2,
                'value_type' => 0,
                'history' => '7d',
            ],
            'tx.http_status' => [
                'name' => 'HTTP Status Code',
                'type' => 2,
                'value_type' => 3,
                'history' => '7d',
            ],
            'auth.login.success' => [
                'name' => 'Login Success Count',
                'type' => 2,
                'value_type' => 3,
                'history' => '7d',
            ],
            'auth.login.failure' => [
                'name' => 'Login Failure Count',
                'type' => 2,
                'value_type' => 3,
                'history' => '7d',
            ],
            'auth.login.success_event' => [
                'name' => 'Login Success Event',
                'type' => 2,
                'value_type' => 4,
                'history' => '7d',
            ],
            'auth.login.failure_event' => [
                'name' => 'Login Failure Event',
                'type' => 2,
                'value_type' => 4,
                'history' => '7d',
            ],
            'entity.persist.success' => [
                'name' => 'Entity Persist Count',
                'type' => 2,
                'value_type' => 3,
                'history' => '7d',
            ],
            'entity.update.success' => [
                'name' => 'Entity Update Count',
                'type' => 2,
                'value_type' => 3,
                'history' => '7d',
            ],
            'entity.remove.success' => [
                'name' => 'Entity Remove Count',
                'type' => 2,
                'value_type' => 3,
                'history' => '7d',
            ],
            'error.exception' => [
                'name' => 'Exception Event',
                'type' => 2,
                'value_type' => 4,
                'history' => '7d',
            ],
        ];
    }

    public function getFullItemKey(string $suffix): string
    {
        return $this->naming->getItemKey($suffix);
    }
}
