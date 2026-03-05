<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Provisioning\Dto;

use Webmozart\Assert\Assert;

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
        Assert::string($data['hostid'] ?? null);
        Assert::string($data['host'] ?? null);
        Assert::string($data['name'] ?? null);

        return new self(
            hostId: $data['hostid'],
            host: $data['host'],
            name: $data['name'],
        );
    }
}
