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
        $widgets = [];
        if (isset($data['widgets']) && is_array($data['widgets'])) {
            foreach ($data['widgets'] as $widgetData) {
                if (is_array($widgetData)) {
                    $widgets[] = DashboardWidgetDto::fromArray($widgetData);
                }
            }
        }

        $displayPeriod = null;
        if (isset($data['display_period'])) {
            $displayPeriod = is_int($data['display_period']) ? $data['display_period'] : null;
        }

        $sortorder = null;
        if (isset($data['sortorder'])) {
            $sortorder = is_int($data['sortorder']) ? $data['sortorder'] : null;
        }

        return new self(
            name: isset($data['name']) && is_string($data['name']) ? $data['name'] : null,
            display_period: $displayPeriod,
            sortorder: $sortorder,
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
