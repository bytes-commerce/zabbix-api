<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class CreateHostGroupDto
{
    /**
     * @param list<array{name: string}> $hostGroups
     */
    public function __construct(
        public array $hostGroups,
    ) {
    }

    /**
     * @return list<array{name: string}>
     */
    public function getHostGroups(): array
    {
        return $this->hostGroups;
    }
}
