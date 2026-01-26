<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class GraphItemDto
{
    public function __construct(
        public string $gitemid,
        public string $itemid,
        public ?int $drawtype,
        public ?int $sortorder,
        public ?string $color,
        public ?int $yaxisside,
        public ?int $calc_fnc,
        public ?int $type,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            gitemid: $data['gitemid'],
            itemid: $data['itemid'],
            drawtype: isset($data['drawtype']) ? (int) $data['drawtype'] : null,
            sortorder: isset($data['sortorder']) ? (int) $data['sortorder'] : null,
            color: $data['color'] ?? null,
            yaxisside: isset($data['yaxisside']) ? (int) $data['yaxisside'] : null,
            calc_fnc: isset($data['calc_fnc']) ? (int) $data['calc_fnc'] : null,
            type: isset($data['type']) ? (int) $data['type'] : null,
        );
    }

    public function getGitemid(): string
    {
        return $this->gitemid;
    }

    public function getItemid(): string
    {
        return $this->itemid;
    }

    public function getDrawtype(): ?int
    {
        return $this->drawtype;
    }

    public function getSortorder(): ?int
    {
        return $this->sortorder;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getYaxisside(): ?int
    {
        return $this->yaxisside;
    }

    public function getCalcFnc(): ?int
    {
        return $this->calc_fnc;
    }

    public function getType(): ?int
    {
        return $this->type;
    }
}
