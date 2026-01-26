<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class IsWritableHostGroupResponseDto
{
    public function __construct(
        public bool $isWritable,
    ) {
    }

    public function getIsWritable(): bool
    {
        return $this->isWritable;
    }
}
