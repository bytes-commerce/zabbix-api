<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

use Webmozart\Assert\Assert;

final readonly class AuditLogDto
{
    public function __construct(
        public string $auditid,
        public string $userid,
        public int $clock,
        public string $action,
        public string $resourcetype,
        public string $resourceid,
        public string $resourcename,
        public ?string $details,
        public ?string $ip,
        public ?string $resource_cuid,
    ) {
    }

    public static function fromArray(array $data): self
    {
        Assert::string($data['auditid'] ?? null);
        Assert::string($data['userid'] ?? null);
        Assert::integerish($data['clock'] ?? null);
        Assert::string($data['action'] ?? null);
        Assert::string($data['resourcetype'] ?? null);
        Assert::string($data['resourceid'] ?? null);
        Assert::string($data['resourcename'] ?? null);

        return new self(
            auditid: $data['auditid'],
            userid: $data['userid'],
            clock: (int) $data['clock'],
            action: $data['action'],
            resourcetype: $data['resourcetype'],
            resourceid: $data['resourceid'],
            resourcename: $data['resourcename'],
            details: isset($data['details']) && is_string($data['details']) ? $data['details'] : null,
            ip: isset($data['ip']) && is_string($data['ip']) ? $data['ip'] : null,
            resource_cuid: isset($data['resource_cuid']) && is_string($data['resource_cuid']) ? $data['resource_cuid'] : null,
        );
    }

    public function getAuditid(): string
    {
        return $this->auditid;
    }

    public function getUserid(): string
    {
        return $this->userid;
    }

    public function getClock(): int
    {
        return $this->clock;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getResourcetype(): string
    {
        return $this->resourcetype;
    }

    public function getResourceid(): string
    {
        return $this->resourceid;
    }

    public function getResourcename(): string
    {
        return $this->resourcename;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function getResourceCuid(): ?string
    {
        return $this->resource_cuid;
    }
}
