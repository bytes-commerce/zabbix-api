<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Provisioning\Dto;

use BytesCommerce\ZabbixApi\Provisioning\ValueObject\DefinitionHash;
use BytesCommerce\ZabbixApi\Provisioning\ValueObject\ManagedKey;
use Webmozart\Assert\Assert;

final readonly class DashboardSpec
{
    public function __construct(
        public string $name,
        public ManagedKey $managedKey,
        public DefinitionHash $hash,
        public array $widgets,
    ) {
    }

    public static function fromArray(array $data): self
    {
        Assert::string($data['name'] ?? null);
        Assert::string($data['managed_key'] ?? null);
        Assert::string($data['hash'] ?? null);
        Assert::isArray($data['widgets'] ?? null);

        return new self(
            name: $data['name'],
            managedKey: ManagedKey::fromString($data['managed_key']),
            hash: DefinitionHash::fromString($data['hash']),
            widgets: $data['widgets'],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'managed_key' => $this->managedKey->value,
            'hash' => $this->hash->value,
            'widgets' => $this->widgets,
        ];
    }
}
