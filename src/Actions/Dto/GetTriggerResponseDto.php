<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Actions\Dto;

final readonly class GetTriggerResponseDto
{
    /**
     * @param TriggerDto[] $triggers
     */
    public function __construct(
        public array $triggers,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            triggers: array_map(TriggerDto::fromArray(...), $data),
        );
    }
}
