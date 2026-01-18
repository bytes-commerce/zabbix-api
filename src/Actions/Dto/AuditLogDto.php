<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Actions\Dto;

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
        return new self(
            auditid: $data['auditid'],
            userid: $data['userid'],
            clock: $data['clock'],
            action: $data['action'],
            resourcetype: $data['resourcetype'],
            resourceid: $data['resourceid'],
            resourcename: $data['resourcename'],
            details: $data['details'] ?? null,
            ip: $data['ip'] ?? null,
            resource_cuid: $data['resource_cuid'] ?? null,
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
