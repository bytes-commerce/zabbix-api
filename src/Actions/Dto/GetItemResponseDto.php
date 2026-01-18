<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Actions\Dto;

final readonly class GetItemResponseDto
{
    /**
     * @param ItemDto[] $items
     */
    public function __construct(
        public array $items,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            items: array_map(ItemDto::fromArray(...), $data),
        );
    }
}
