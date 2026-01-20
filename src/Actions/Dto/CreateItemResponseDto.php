<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class CreateItemResponseDto
{
    public function __construct(
        public array $itemids,
    ) {
    }
}
