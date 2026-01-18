<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Actions\Dto;

final readonly class GetAuditLogResponseDto
{
    /**
     * @param AuditLogDto[] $auditlogs
     */
    public function __construct(
        public array $auditlogs,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            auditlogs: array_map(AuditLogDto::fromArray(...), $data),
        );
    }
}
