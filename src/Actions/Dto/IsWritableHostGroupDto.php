<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class IsWritableHostGroupDto
{
    /**
     * @param list<string>|null $groupids
     * @param list<string>|null $hostids
     */
    public function __construct(
        public ?array $groupids,
        public ?array $hostids,
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
}
