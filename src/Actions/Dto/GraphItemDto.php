<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

use Webmozart\Assert\Assert;

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
        Assert::string($data['gitemid'] ?? null);
        Assert::string($data['itemid'] ?? null);

        $drawtype = null;
        if (isset($data['drawtype'])) {
            Assert::integerish($data['drawtype']);
            $drawtype = (int) $data['drawtype'];
        }

        $sortorder = null;
        if (isset($data['sortorder'])) {
            Assert::integerish($data['sortorder']);
            $sortorder = (int) $data['sortorder'];
        }

        $yaxisside = null;
        if (isset($data['yaxisside'])) {
            Assert::integerish($data['yaxisside']);
            $yaxisside = (int) $data['yaxisside'];
        }

        $calcFnc = null;
        if (isset($data['calc_fnc'])) {
            Assert::integerish($data['calc_fnc']);
            $calcFnc = (int) $data['calc_fnc'];
        }

        $type = null;
        if (isset($data['type'])) {
            Assert::integerish($data['type']);
            $type = (int) $data['type'];
        }

        return new self(
            gitemid: $data['gitemid'],
            itemid: $data['itemid'],
            drawtype: $drawtype,
            sortorder: $sortorder,
            color: isset($data['color']) && is_string($data['color']) ? $data['color'] : null,
            yaxisside: $yaxisside,
            calc_fnc: $calcFnc,
            type: $type,
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
