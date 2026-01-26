<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class DeleteDashboardDto
{
    /**
     * @param list<string> $dashboardIds
     */
    public function __construct(
        public array $dashboardIds,
    ) {
    }

    /**
     * @return list<string>
     */
    public function getDashboardIds(): array
    {
        return $this->dashboardIds;
    }
}
