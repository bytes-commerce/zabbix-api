<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class GetGraphResponseDto
{
    /**
     * @param list<GraphDto> $graphs
     */
    public function __construct(
        public array $graphs,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            graphs: array_map(GraphDto::fromArray(...), $data),
        );
    }

    /**
     * @return list<GraphDto>
     */
    public function getGraphs(): array
    {
        return $this->graphs;
    }
}
