<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class GetTriggerResponseDto
{
    /**
     * @param list<TriggerDto> $triggers
     */
    public function __construct(
        public array $triggers,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $triggers = [];
        foreach ($data as $item) {
            if (is_array($item)) {
                $triggers[] = TriggerDto::fromArray($item);
            }
        }

        return new self(triggers: $triggers);
    }
}
