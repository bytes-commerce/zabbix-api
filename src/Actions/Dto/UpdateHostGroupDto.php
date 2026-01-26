<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class UpdateHostGroupDto
{
    /**
     * @param list<array{groupid: string, name?: string}> $hostGroups
     */
    public function __construct(
        public array $hostGroups,
    ) {
    }

    /**
     * @return list<array{groupid: string, name?: string}>
     */
    public function getHostGroups(): array
    {
        return $this->hostGroups;
    }
}
