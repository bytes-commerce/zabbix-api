<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class IsReadableHostGroupResponseDto
{
    public function __construct(
        public bool $isReadable,
    ) {
    }

    public function getIsReadable(): bool
    {
        return $this->isReadable;
    }
}
