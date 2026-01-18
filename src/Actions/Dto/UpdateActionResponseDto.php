<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Actions\Dto;

final readonly class UpdateActionResponseDto
{
    public function __construct(
        public array $actionids,
    ) {
    }
}
