<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class GraphDto
{
    /**
     * @param list<GraphItemDto> $gitems
     */
    public function __construct(
        public string $graphid,
        public string $name,
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
        public array $gitems,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $gitems = isset($data['gitems']) && is_array($data['gitems'])
            ? array_map(GraphItemDto::fromArray(...), $data['gitems'])
            : [];

        return new self(
            graphid: $data['graphid'],
            name: $data['name'],
            width: isset($data['width']) ? (int) $data['width'] : null,
            height: isset($data['height']) ? (int) $data['height'] : null,
            yaxismin: isset($data['yaxismin']) ? (float) $data['yaxismin'] : null,
            yaxismax: isset($data['yaxismax']) ? (float) $data['yaxismax'] : null,
            show_work_period: isset($data['show_work_period']) ? (int) $data['show_work_period'] : null,
            show_triggers: isset($data['show_triggers']) ? (int) $data['show_triggers'] : null,
            graphtype: isset($data['graphtype']) ? (int) $data['graphtype'] : null,
            show_legend: isset($data['show_legend']) ? (int) $data['show_legend'] : null,
            show_3d: isset($data['show_3d']) ? (int) $data['show_3d'] : null,
            percent_left: isset($data['percent_left']) ? (float) $data['percent_left'] : null,
            percent_right: isset($data['percent_right']) ? (float) $data['percent_right'] : null,
            ymin_type: isset($data['ymin_type']) ? (int) $data['ymin_type'] : null,
            ymax_type: isset($data['ymax_type']) ? (int) $data['ymax_type'] : null,
            ymin_itemid: $data['ymin_itemid'] ?? null,
            ymax_itemid: $data['ymax_itemid'] ?? null,
            gitems: $gitems,
        );
    }

    public function getGraphid(): string
    {
        return $this->graphid;
    }

    public function getName(): string
    {
        return $this->name;
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

    /**
     * @return list<GraphItemDto>
     */
    public function getGitems(): array
    {
        return $this->gitems;
    }
}
