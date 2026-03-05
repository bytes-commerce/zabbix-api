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
        $dashboards = [];
        foreach ($data as $item) {
            if (is_array($item)) {
                $dashboards[] = DashboardDto::fromArray($item);
            }
        }

        return new self(dashboards: $dashboards);
    }

    /**
     * @return list<DashboardDto>
     */
    public function getDashboards(): array
    {
        return $this->dashboards;
    }
}
