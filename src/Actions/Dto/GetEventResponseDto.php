<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class GetEventResponseDto
{
    /**
     * @param EventDto[] $events
     */
    public function __construct(
        public array $events,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            events: array_map(EventDto::fromArray(...), $data),
        );
    }
}
