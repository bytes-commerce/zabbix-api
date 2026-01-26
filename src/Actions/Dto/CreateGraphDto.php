<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class CreateGraphDto
{
    /**
     * @param list<CreateSingleGraphDto> $graphs
     */
    public function __construct(
        public array $graphs,
    ) {
    }

    /**
     * @return list<CreateSingleGraphDto>
     */
    public function getGraphs(): array
    {
        return $this->graphs;
    }
}
