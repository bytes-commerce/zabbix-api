<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class UpdateGraphDto
{
    /**
     * @param list<UpdateSingleGraphDto> $graphs
     */
    public function __construct(
        public array $graphs,
    ) {
    }

    /**
     * @return list<UpdateSingleGraphDto>
     */
    public function getGraphs(): array
    {
        return $this->graphs;
    }
}
