<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class GetActionResponseDto
{
    /**
     * @param ActionDto[] $actions
     */
    public function __construct(
        public array $actions,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            actions: array_map(ActionDto::fromArray(...), $data),
        );
    }
}
