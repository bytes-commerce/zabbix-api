<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class GetHostGroupDto
{
    /**
     * @param list<string>|null $groupids
     * @param list<string>|null $hostids
     * @param array<string, mixed>|null $filter
     */
    public function __construct(
        public ?array $groupids,
        public ?array $hostids,
        public ?array $filter,
        public ?string $output,
        public ?bool $selectHosts,
        public ?bool $selectTemplates,
        public ?int $sortfield,
        public ?string $sortorder,
        public ?int $limit,
        public ?bool $preservekeys,
    ) {
    }

    /**
     * @return list<string>|null
     */
    public function getGroupids(): ?array
    {
        return $this->groupids;
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

    public function getSelectHosts(): ?bool
    {
        return $this->selectHosts;
    }

    public function getSelectTemplates(): ?bool
    {
        return $this->selectTemplates;
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
