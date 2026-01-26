<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class DashboardPageDto
{
    /**
     * @param list<DashboardWidgetDto> $widgets
     */
    public function __construct(
        public ?string $name,
        public ?int $display_period,
        public ?int $sortorder,
        public array $widgets,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $widgets = isset($data['widgets']) && is_array($data['widgets'])
            ? array_map(DashboardWidgetDto::fromArray(...), $data['widgets'])
            : [];

        return new self(
            name: $data['name'] ?? null,
            display_period: isset($data['display_period']) ? (int) $data['display_period'] : null,
            sortorder: isset($data['sortorder']) ? (int) $data['sortorder'] : null,
            widgets: $widgets,
        );
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDisplayPeriod(): ?int
    {
        return $this->display_period;
    }

    public function getSortorder(): ?int
    {
        return $this->sortorder;
    }

    /**
     * @return list<DashboardWidgetDto>
     */
    public function getWidgets(): array
    {
        return $this->widgets;
    }
}
