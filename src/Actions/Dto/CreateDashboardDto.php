<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class CreateDashboardDto
{
    /**
     * @param list<array<string, mixed>> $dashboards
     */
    public function __construct(
        public array $dashboards,
    ) {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getDashboards(): array
    {
        return $this->dashboards;
    }
}
