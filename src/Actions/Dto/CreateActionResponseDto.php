<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Actions\Dto;

final readonly class CreateActionResponseDto
{
    public function __construct(
        public array $actionids,
    ) {
    }
}
