<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class DeleteHostGroupDto
{
    /**
     * @param list<string> $groupIds
     */
    public function __construct(
        public array $groupIds,
    ) {
    }

    /**
     * @return list<string>
     */
    public function getGroupIds(): array
    {
        return $this->groupIds;
    }
}
