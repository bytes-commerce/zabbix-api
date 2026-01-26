<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class GetGraphDto
{
    /**
     * @param list<string>|null $graphids
     * @param list<string>|null $itemids
     * @param list<string>|null $hostids
     * @param array<string, mixed>|null $filter
     */
    public function __construct(
        public ?array $graphids,
        public ?array $itemids,
        public ?array $hostids,
        public ?array $filter,
        public ?string $output,
        public ?bool $selectGraphItems,
        public ?bool $selectHosts,
        public ?bool $selectItems,
        public ?bool $selectGraphDiscovery,
        public ?int $sortfield,
        public ?string $sortorder,
        public ?int $limit,
        public ?bool $preservekeys,
    ) {
    }

    /**
     * @return list<string>|null
     */
    public function getGraphids(): ?array
    {
        return $this->graphids;
    }

    /**
     * @return list<string>|null
     */
    public function getItemids(): ?array
    {
        return $this->itemids;
    }

    /**
     * @return list<string>|null
     */
    public function getHostids(): ?array
    {
        return $this->hostids;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getFilter(): ?array
    {
        return $this->filter;
    }

    public function getOutput(): ?string
    {
        return $this->output;
    }

    public function getSelectGraphItems(): ?bool
    {
        return $this->selectGraphItems;
    }

    public function getSelectHosts(): ?bool
    {
        return $this->selectHosts;
    }

    public function getSelectItems(): ?bool
    {
        return $this->selectItems;
    }

    public function getSelectGraphDiscovery(): ?bool
    {
        return $this->selectGraphDiscovery;
    }

    public function getSortfield(): ?int
    {
        return $this->sortfield;
    }

    public function getSortorder(): ?string
    {
        return $this->sortorder;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getPreservekeys(): ?bool
    {
        return $this->preservekeys;
    }
}
