<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class DashboardDto
{
    /**
     * @param list<DashboardPageDto> $pages
     */
    public function __construct(
        public string $dashboardid,
        public string $name,
        public ?int $private,
        public ?string $userid,
        public ?int $display_period,
        public ?int $auto_start,
        public array $pages,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $pages = isset($data['pages']) && is_array($data['pages'])
            ? array_map(DashboardPageDto::fromArray(...), $data['pages'])
            : [];

        return new self(
            dashboardid: $data['dashboardid'],
            name: $data['name'],
            private: isset($data['private']) ? (int) $data['private'] : null,
            userid: $data['userid'] ?? null,
            display_period: isset($data['display_period']) ? (int) $data['display_period'] : null,
            auto_start: isset($data['auto_start']) ? (int) $data['auto_start'] : null,
            pages: $pages,
        );
    }

    public function getDashboardid(): string
    {
        return $this->dashboardid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrivate(): ?int
    {
        return $this->private;
    }

    public function getUserid(): ?string
    {
        return $this->userid;
    }

    public function getDisplayPeriod(): ?int
    {
        return $this->display_period;
    }

    public function getAutoStart(): ?int
    {
        return $this->auto_start;
    }

    /**
     * @return list<DashboardPageDto>
     */
    public function getPages(): array
    {
        return $this->pages;
    }
}
