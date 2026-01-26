<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class UpdateGraphResponseDto
{
    /**
     * @param list<string> $graphids
     */
    public function __construct(
        public array $graphids,
    ) {
    }

    /**
     * @return list<string>
     */
    public function getGraphids(): array
    {
        return $this->graphids;
    }
}
