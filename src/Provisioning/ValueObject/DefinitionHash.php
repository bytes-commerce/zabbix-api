<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Provisioning\ValueObject;

final readonly class DefinitionHash
{
    public function __construct(
        public string $value,
    ) {
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function fromData(array $data): self
    {
        return new self(hash('sha256', json_encode($data, \JSON_THROW_ON_ERROR)));
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
