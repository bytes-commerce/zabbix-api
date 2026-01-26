<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class UpdateDashboardResponseDto
{
    /**
     * @param list<string> $dashboardids
     */
    public function __construct(
        public array $dashboardids,
    ) {
    }

    /**
     * @return list<string>
     */
    public function getDashboardids(): array
    {
        return $this->dashboardids;
    }
}
