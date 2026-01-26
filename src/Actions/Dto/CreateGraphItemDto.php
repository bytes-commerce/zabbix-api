<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class CreateGraphItemDto
{
    public function __construct(
        public string $itemid,
        public ?int $drawtype,
        public ?int $sortorder,
        public ?string $color,
        public ?int $yaxisside,
        public ?int $calc_fnc,
        public ?int $type,
    ) {
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
