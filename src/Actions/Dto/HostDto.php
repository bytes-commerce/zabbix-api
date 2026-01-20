<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

use BytesCommerce\ZabbixApi\Enums\StatusEnum;

final readonly class HostDto
{
    public function __construct(
        public string $hostid,
        public string $host,
        public ?string $name,
        public StatusEnum $status,
        public ?array $interfaces,
        public ?array $groups,
        public ?array $templates,
        public ?array $macros,
        public ?array $tags,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            hostid: $data['hostid'],
            host: $data['host'],
            name: $data['name'] ?? null,
            status: StatusEnum::from($data['status']),
            interfaces: $data['interfaces'] ?? null,
            groups: $data['groups'] ?? null,
            templates: $data['templates'] ?? null,
            macros: $data['macros'] ?? null,
            tags: $data['tags'] ?? null,
        );
    }

    public function getHostid(): string
    {
        return $this->hostid;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getStatus(): StatusEnum
    {
        return $this->status;
    }

    public function getInterfaces(): ?array
    {
        return $this->interfaces;
    }

    public function getGroups(): ?array
    {
        return $this->groups;
    }

    public function getTemplates(): ?array
    {
        return $this->templates;
    }

    public function getMacros(): ?array
    {
        return $this->macros;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }
}
