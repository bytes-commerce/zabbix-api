<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class GetDashboardDto
{
    /**
     * @param list<string>|null $dashboardids
     * @param array<string, mixed>|null $filter
     */
    public function __construct(
        public ?array $dashboardids,
        public ?array $filter,
        public ?string $output,
        public ?bool $selectPages,
        public ?bool $selectUsers,
        public ?bool $selectUserGroups,
        public ?int $sortfield,
        public ?string $sortorder,
        public ?int $limit,
        public ?bool $preservekeys,
    ) {
    }

    /**
     * @return list<string>|null
     */
    public function getDashboardids(): ?array
    {
        return $this->dashboardids;
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

    public function getSelectPages(): ?bool
    {
        return $this->selectPages;
    }

    public function getSelectUsers(): ?bool
    {
        return $this->selectUsers;
    }

    public function getSelectUserGroups(): ?bool
    {
        return $this->selectUserGroups;
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
