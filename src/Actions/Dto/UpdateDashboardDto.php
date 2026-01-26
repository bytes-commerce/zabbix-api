<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class UpdateDashboardDto
{
    /**
     * @param list<array{dashboardid: string, name?: string, pages?: list<array{name?: string, widgets?: list<array{type: string, name?: string, x?: int, y?: int, width?: int, height?: int, fields?: array<string, mixed>, view_mode?: string}>, private?: int, userid?: string, display_period?: int, auto_start?: int}> $dashboards
     */
    public function __construct(
        public array $dashboards,
    ) {
    }

    /**
     * @return list<array{dashboardid: string, name?: string, pages?: list<array{name?: string, widgets?: list<array{type: string, name?: string, x?: int, y?: int, width?: int, height?: int, fields?: array<string, mixed>, view_mode?: string}>, private?: int, userid?: string, display_period?: int, auto_start?: int}>
     */
    public function getDashboards(): array
    {
        return $this->dashboards;
    }
}
