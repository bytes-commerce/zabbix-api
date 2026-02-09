<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Provisioning\Dto;

final readonly class HostInfo
{
    public function __construct(
        public string $hostId,
        public string $host,
        public string $name,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            hostId: $data['hostid'],
            host: $data['host'],
            name: $data['name'],
        );
    }
}
