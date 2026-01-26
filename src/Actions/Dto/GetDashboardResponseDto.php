<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class GetDashboardResponseDto
{
    /**
     * @param list<DashboardDto> $dashboards
     */
    public function __construct(
        public array $dashboards,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            dashboards: array_map(DashboardDto::fromArray(...), $data),
        );
    }

    /**
     * @return list<DashboardDto>
     */
    public function getDashboards(): array
    {
        return $this->dashboards;
    }
}
