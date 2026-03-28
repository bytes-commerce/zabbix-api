<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class GetItemResponseDto
{
    /**
     * @param list<ItemDto> $items
     */
    public function __construct(
        public array $items,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $items = [];
        foreach ($data as $item) {
            if (is_array($item)) {
                $items[] = ItemDto::fromArray($item);
            }
        }

        return new self(items: $items);
    }
}
