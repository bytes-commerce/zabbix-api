<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class GetActionResponseDto
{
    /**
     * @param list<ActionDto> $actions
     */
    public function __construct(
        public array $actions,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $actions = [];
        foreach ($data as $item) {
            if (is_array($item)) {
                $actions[] = ActionDto::fromArray($item);
            }
        }

        return new self(actions: $actions);
    }
}
