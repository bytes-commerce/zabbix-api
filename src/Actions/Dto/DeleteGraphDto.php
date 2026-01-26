<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class DeleteGraphDto
{
    /**
     * @param list<string> $graphIds
     */
    public function __construct(
        public array $graphIds,
    ) {
    }

    /**
     * @return list<string>
     */
    public function getGraphIds(): array
    {
        return $this->graphIds;
    }
}
