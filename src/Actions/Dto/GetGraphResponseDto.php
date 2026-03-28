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
        $graphs = [];
        foreach ($data as $item) {
            if (is_array($item)) {
                $graphs[] = GraphDto::fromArray($item);
            }
        }

        return new self(graphs: $graphs);
    }

    /**
     * @return list<GraphDto>
     */
    public function getGraphs(): array
    {
        return $this->graphs;
    }
}
