<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Provisioning\ValueObject;

final readonly class ManagedKey
{
    public function __construct(
        public string $value,
    ) {
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function fromComponents(string $app, string $env, string $hostId): self
    {
        return new self(\sprintf('%s_%s_%s', $app, $env, $hostId));
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
