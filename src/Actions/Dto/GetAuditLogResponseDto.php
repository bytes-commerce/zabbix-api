<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class GetAuditLogResponseDto
{
    /**
     * @param list<AuditLogDto> $auditlogs
     */
    public function __construct(
        public array $auditlogs,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $auditlogs = [];
        foreach ($data as $item) {
            if (is_array($item)) {
                $auditlogs[] = AuditLogDto::fromArray($item);
            }
        }

        return new self(auditlogs: $auditlogs);
    }
}
