<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

use Webmozart\Assert\Assert;

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
        Assert::string($data['graphid'] ?? null);
        Assert::string($data['name'] ?? null);

        $gitems = [];
        if (isset($data['gitems']) && is_array($data['gitems'])) {
            foreach ($data['gitems'] as $itemData) {
                if (is_array($itemData)) {
                    $gitems[] = GraphItemDto::fromArray($itemData);
                }
            }
        }

        $width = null;
        if (isset($data['width'])) {
            Assert::integerish($data['width']);
            $width = (int) $data['width'];
        }

        $height = null;
        if (isset($data['height'])) {
            Assert::integerish($data['height']);
            $height = (int) $data['height'];
        }

        $yaxismin = null;
        if (isset($data['yaxismin'])) {
            Assert::numeric($data['yaxismin']);
            $yaxismin = (float) $data['yaxismin'];
        }

        $yaxismax = null;
        if (isset($data['yaxismax'])) {
            Assert::numeric($data['yaxismax']);
            $yaxismax = (float) $data['yaxismax'];
        }

        $showWorkPeriod = null;
        if (isset($data['show_work_period'])) {
            Assert::integerish($data['show_work_period']);
            $showWorkPeriod = (int) $data['show_work_period'];
        }

        $showTriggers = null;
        if (isset($data['show_triggers'])) {
            Assert::integerish($data['show_triggers']);
            $showTriggers = (int) $data['show_triggers'];
        }

        $graphtype = null;
        if (isset($data['graphtype'])) {
            Assert::integerish($data['graphtype']);
            $graphtype = (int) $data['graphtype'];
        }

        $showLegend = null;
        if (isset($data['show_legend'])) {
            Assert::integerish($data['show_legend']);
            $showLegend = (int) $data['show_legend'];
        }

        $show3d = null;
        if (isset($data['show_3d'])) {
            Assert::integerish($data['show_3d']);
            $show3d = (int) $data['show_3d'];
        }

        $percentLeft = null;
        if (isset($data['percent_left'])) {
            Assert::numeric($data['percent_left']);
            $percentLeft = (float) $data['percent_left'];
        }

        $percentRight = null;
        if (isset($data['percent_right'])) {
            Assert::numeric($data['percent_right']);
            $percentRight = (float) $data['percent_right'];
        }

        $yminType = null;
        if (isset($data['ymin_type'])) {
            Assert::integerish($data['ymin_type']);
            $yminType = (int) $data['ymin_type'];
        }

        $ymaxType = null;
        if (isset($data['ymax_type'])) {
            Assert::integerish($data['ymax_type']);
            $ymaxType = (int) $data['ymax_type'];
        }

        return new self(
            graphid: $data['graphid'],
            name: $data['name'],
            width: $width,
            height: $height,
            yaxismin: $yaxismin,
            yaxismax: $yaxismax,
            show_work_period: $showWorkPeriod,
            show_triggers: $showTriggers,
            graphtype: $graphtype,
            show_legend: $showLegend,
            show_3d: $show3d,
            percent_left: $percentLeft,
            percent_right: $percentRight,
            ymin_type: $yminType,
            ymax_type: $ymaxType,
            ymin_itemid: isset($data['ymin_itemid']) && is_string($data['ymin_itemid']) ? $data['ymin_itemid'] : null,
            ymax_itemid: isset($data['ymax_itemid']) && is_string($data['ymax_itemid']) ? $data['ymax_itemid'] : null,
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
