<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class ExistsHostGroupResponseDto
{
    public function __construct(
        public bool $exists,
    ) {
    }

    public function getExists(): bool
    {
        return $this->exists;
    }
}
