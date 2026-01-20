<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class GetAlertResponseDto
{
    /**
     * @param AlertDto[] $alerts
     */
    public function __construct(
        public array $alerts,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            alerts: array_map(AlertDto::fromArray(...), $data),
        );
    }
}
