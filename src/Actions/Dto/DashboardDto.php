<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

use Webmozart\Assert\Assert;

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
        Assert::string($data['dashboardid'] ?? null);
        Assert::string($data['name'] ?? null);

        $pages = [];
        if (isset($data['pages']) && is_array($data['pages'])) {
            foreach ($data['pages'] as $pageData) {
                if (is_array($pageData)) {
                    $pages[] = DashboardPageDto::fromArray($pageData);
                }
            }
        }

        $private = null;
        if (isset($data['private'])) {
            Assert::integerish($data['private']);
            $private = (int) $data['private'];
        }

        $displayPeriod = null;
        if (isset($data['display_period'])) {
            Assert::integerish($data['display_period']);
            $displayPeriod = (int) $data['display_period'];
        }

        $autoStart = null;
        if (isset($data['auto_start'])) {
            Assert::integerish($data['auto_start']);
            $autoStart = (int) $data['auto_start'];
        }

        return new self(
            dashboardid: $data['dashboardid'],
            name: $data['name'],
            private: $private,
            userid: isset($data['userid']) && is_string($data['userid']) ? $data['userid'] : null,
            display_period: $displayPeriod,
            auto_start: $autoStart,
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
