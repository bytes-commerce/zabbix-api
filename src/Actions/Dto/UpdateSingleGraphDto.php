<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class UpdateSingleGraphDto
{
    /**
     * @param list<UpdateGraphItemDto> $gitems
     */
    public function __construct(
        public string $graphid,
        public ?string $name,
        public ?array $gitems,
        public ?int $width,
        public ?int $height,
        public ?float $yaxismin,
        public ?float $yaxismax,
        public ?int $show_work_period,
        public ?int $show_triggers,
        public ?int $graphtype,
        public ?int $show_legend,
        public ?int $show_3d,
        public ?float $percent_left,
        public ?float $percent_right,
        public ?int $ymin_type,
        public ?int $ymax_type,
        public ?string $ymin_itemid,
        public ?string $ymax_itemid,
    ) {
    }

    public function getGraphid(): string
    {
        return $this->graphid;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return list<UpdateGraphItemDto>|null
     */
    public function getGitems(): ?array
    {
        return $this->gitems;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function getYaxismin(): ?float
    {
        return $this->yaxismin;
    }

    public function getYaxismax(): ?float
    {
        return $this->yaxismax;
    }

    public function getShowWorkPeriod(): ?int
    {
        return $this->show_work_period;
    }

    public function getShowTriggers(): ?int
    {
        return $this->show_triggers;
    }

    public function getGraphtype(): ?int
    {
        return $this->graphtype;
    }

    public function getShowLegend(): ?int
    {
        return $this->show_legend;
    }

    public function getShow3d(): ?int
    {
        return $this->show_3d;
    }

    public function getPercentLeft(): ?float
    {
        return $this->percent_left;
    }

    public function getPercentRight(): ?float
    {
        return $this->percent_right;
    }

    public function getYminType(): ?int
    {
        return $this->ymin_type;
    }

    public function getYmaxType(): ?int
    {
        return $this->ymax_type;
    }

    public function getYminItemid(): ?string
    {
        return $this->ymin_itemid;
    }

    public function getYmaxItemid(): ?string
    {
        return $this->ymax_itemid;
    }
}
