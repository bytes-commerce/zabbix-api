<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

use BytesCommerce\ZabbixApi\Enums\StatusEnum;
use Webmozart\Assert\Assert;

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
        Assert::string($data['hostid'] ?? null);
        Assert::string($data['host'] ?? null);
        Assert::integerish($data['status'] ?? null);

        return new self(
            hostid: $data['hostid'],
            host: $data['host'],
            name: isset($data['name']) && is_string($data['name']) ? $data['name'] : null,
            status: StatusEnum::from((int) $data['status']),
            interfaces: isset($data['interfaces']) && is_array($data['interfaces']) ? $data['interfaces'] : null,
            groups: isset($data['groups']) && is_array($data['groups']) ? $data['groups'] : null,
            templates: isset($data['templates']) && is_array($data['templates']) ? $data['templates'] : null,
            macros: isset($data['macros']) && is_array($data['macros']) ? $data['macros'] : null,
            tags: isset($data['tags']) && is_array($data['tags']) ? $data['tags'] : null,
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
