<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Contract;

interface ItemDefinitionProviderInterface
{
    public function getAllItemDefinitions(): array;

    public function getFullItemKey(string $suffix): string;

    public function getHostId(): ?string;

    public function setHostId(string $hostId): void;

    public function getItemIdForKey(string $key): ?string;

    public function setItemId(string $key, string $itemId): void;
}
