<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Actions\Dto;

final readonly class UpdateItemResponseDto
{
    public function __construct(
        public array $itemids,
    ) {
    }
}
