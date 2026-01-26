<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class MassAddHostGroupResponseDto
{
    /**
     * @param list<string> $groupids
     */
    public function __construct(
        public array $groupids,
    ) {
    }

    /**
     * @return list<string>
     */
    public function getGroupids(): array
    {
        return $this->groupids;
    }
}
