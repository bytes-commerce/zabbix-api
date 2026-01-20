<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class CreateActionResponseDto
{
    public function __construct(
        public array $actionids,
    ) {
    }
}
