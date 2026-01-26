<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class CreateDashboardDto
{
    /**
     * @param list<array{name: string, pages?: list<array{name?: string, widgets?: list<array{type: string, name?: string, x?: int, y?: int, width?: int, height?: int, fields?: array<string, mixed>, view_mode?: string}>}> $dashboards
     */
    public function __construct(
        public array $dashboards,
    ) {
    }

    /**
     * @return list<array{name: string, pages?: list<array{name?: string, widgets?: list<array{type: string, name?: string, x?: int, y?: int, width?: int, height?: int, fields?: array<string, mixed>, view_mode?: string}>}>
     */
    public function getDashboards(): array
    {
        return $this->dashboards;
    }
}
