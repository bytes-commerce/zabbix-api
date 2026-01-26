<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class ExistsHostGroupDto
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
        public ?string $name,
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

    public function getName(): ?string
    {
        return $this->name;
    }
}
