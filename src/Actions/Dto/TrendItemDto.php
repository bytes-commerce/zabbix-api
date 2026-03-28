<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class TrendItemDto
{
    public function __construct(
        public string $itemid,
        public int $clock,
        public int $num,
        public float $valueMin,
        public float $valueAvg,
        public float $valueMax,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $itemid = $data['itemid'] ?? '';
        $clock = $data['clock'] ?? 0;
        $num = $data['num'] ?? 0;
        $valueMin = $data['value_min'] ?? 0;
        $valueAvg = $data['value_avg'] ?? 0;
        $valueMax = $data['value_max'] ?? 0;

        return new self(
            itemid: is_string($itemid) ? $itemid : '',
            clock: is_int($clock) ? $clock : 0,
            num: is_int($num) ? $num : 0,
            valueMin: is_numeric($valueMin) ? (float) $valueMin : 0.0,
            valueAvg: is_numeric($valueAvg) ? (float) $valueAvg : 0.0,
            valueMax: is_numeric($valueMax) ? (float) $valueMax : 0.0,
        );
    }

    public function getTimestamp(): \DateTimeImmutable
    {
        return (new \DateTimeImmutable())->setTimestamp($this->clock);
    }
}