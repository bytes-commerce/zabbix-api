<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class HostGroupDto
{
    public function __construct(
        public string $groupid,
        public string $name,
        public ?int $flags,
        public ?int $internal,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            groupid: $data['groupid'],
            name: $data['name'],
            flags: isset($data['flags']) ? (int) $data['flags'] : null,
            internal: isset($data['internal']) ? (int) $data['internal'] : null,
        );
    }

    public function getGroupid(): string
    {
        return $this->groupid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFlags(): ?int
    {
        return $this->flags;
    }

    public function getInternal(): ?int
    {
        return $this->internal;
    }
}
