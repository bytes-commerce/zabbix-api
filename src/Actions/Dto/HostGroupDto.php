<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

use Webmozart\Assert\Assert;

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
        Assert::string($data['groupid'] ?? null);
        Assert::string($data['name'] ?? null);

        $flags = null;
        if (isset($data['flags'])) {
            Assert::integerish($data['flags']);
            $flags = (int) $data['flags'];
        }

        $internal = null;
        if (isset($data['internal'])) {
            Assert::integerish($data['internal']);
            $internal = (int) $data['internal'];
        }

        return new self(
            groupid: $data['groupid'],
            name: $data['name'],
            flags: $flags,
            internal: $internal,
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
