<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Contract;

interface ZabbixSenderInterface
{
    public function pushNumeric(string $key, float|int $value, int $clock, array $tags, string $correlationId): void;

    public function pushEvent(string $key, array $payload, int $clock, string $correlationId): void;
}
