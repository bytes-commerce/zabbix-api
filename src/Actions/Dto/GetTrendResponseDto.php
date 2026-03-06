<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class GetTrendResponseDto
{
    /**
     * @param list<TrendItemDto> $items
     */
    public function __construct(
        public array $items = [],
    ) {
    }

    /**
     * @param array<int, array<string, mixed>> $data
     */
    public static function fromArray(array $data): self
    {
        $items = [];
        foreach ($data as $item) {
            if (!is_array($item)) {
                continue;
            }
            $items[] = TrendItemDto::fromArray($item);
        }

        return new self($items);
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return list<float>
     */
    public function getValues(): array
    {
        return array_map(static fn (TrendItemDto $item) => $item->valueAvg, $this->items);
    }

    /**
     * @return list<float>
     */
    public function getMinValues(): array
    {
        return array_map(static fn (TrendItemDto $item) => $item->valueMin, $this->items);
    }

    /**
     * @return list<float>
     */
    public function getMaxValues(): array
    {
        return array_map(static fn (TrendItemDto $item) => $item->valueMax, $this->items);
    }

    public function getAverageValue(): float
    {
        $values = $this->getValues();
        if ($values === []) {
            return 0.0;
        }

        return array_sum($values) / count($values);
    }

    public function getMinValue(): float
    {
        $values = $this->getMinValues();
        if ($values === []) {
            return 0.0;
        }

        return min($values);
    }

    public function getMaxValue(): float
    {
        $values = $this->getMaxValues();
        if ($values === []) {
            return 0.0;
        }

        return max($values);
    }
}