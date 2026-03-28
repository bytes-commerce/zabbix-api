<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class GetEventResponseDto
{
    /**
     * @param list<EventDto> $events
     */
    public function __construct(
        public array $events,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $events = [];
        foreach ($data as $item) {
            if (is_array($item)) {
                $events[] = EventDto::fromArray($item);
            }
        }

        return new self(events: $events);
    }
}
