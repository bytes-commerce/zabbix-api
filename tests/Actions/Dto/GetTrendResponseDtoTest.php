<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Tests\Actions\Dto;

use BytesCommerce\ZabbixApi\Actions\Dto\GetTrendResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\TrendItemDto;
use PHPUnit\Framework\TestCase;

final class GetTrendResponseDtoTest extends TestCase
{
    public function testFromEmptyArray(): void
    {
        $dto = GetTrendResponseDto::fromArray([]);

        $this->assertTrue($dto->isEmpty());
        $this->assertCount(0, $dto->items);
        $this->assertSame(0, $dto->count());
    }

    public function testFromArrayCreatesItems(): void
    {
        $data = [
            [
                'itemid' => '123',
                'clock' => 1709500000,
                'num' => 60,
                'value_min' => '10.5',
                'value_avg' => '25.3',
                'value_max' => '50.0',
            ],
            [
                'itemid' => '456',
                'clock' => 1709500100,
                'num' => 120,
                'value_min' => '5.0',
                'value_avg' => '15.0',
                'value_max' => '30.0',
            ],
        ];

        $dto = GetTrendResponseDto::fromArray($data);

        $this->assertFalse($dto->isEmpty());
        $this->assertCount(2, $dto->items);
        $this->assertSame(2, $dto->count());
    }

    public function testGetValuesReturnsAverageValues(): void
    {
        $items = [
            new TrendItemDto('1', 1709500000, 60, 10.0, 25.0, 50.0),
            new TrendItemDto('2', 1709500100, 120, 5.0, 15.0, 30.0),
        ];

        $dto = new GetTrendResponseDto($items);

        $values = $dto->getValues();

        $this->assertSame([25.0, 15.0], $values);
    }

    public function testGetMinValuesReturnsMinValues(): void
    {
        $items = [
            new TrendItemDto('1', 1709500000, 60, 10.0, 25.0, 50.0),
            new TrendItemDto('2', 1709500100, 120, 5.0, 15.0, 30.0),
        ];

        $dto = new GetTrendResponseDto($items);

        $values = $dto->getMinValues();

        $this->assertSame([10.0, 5.0], $values);
    }

    public function testGetMaxValuesReturnsMaxValues(): void
    {
        $items = [
            new TrendItemDto('1', 1709500000, 60, 10.0, 25.0, 50.0),
            new TrendItemDto('2', 1709500100, 120, 5.0, 15.0, 30.0),
        ];

        $dto = new GetTrendResponseDto($items);

        $values = $dto->getMaxValues();

        $this->assertSame([50.0, 30.0], $values);
    }

    public function testGetAverageValueReturnsZeroForEmptyItems(): void
    {
        $dto = new GetTrendResponseDto([]);

        $this->assertSame(0.0, $dto->getAverageValue());
    }

    public function testGetAverageValueCalculatesCorrectly(): void
    {
        $items = [
            new TrendItemDto('1', 1709500000, 60, 10.0, 20.0, 50.0),
            new TrendItemDto('2', 1709500100, 120, 5.0, 40.0, 30.0),
        ];

        $dto = new GetTrendResponseDto($items);

        $this->assertSame(30.0, $dto->getAverageValue());
    }

    public function testGetMinValueReturnsZeroForEmptyItems(): void
    {
        $dto = new GetTrendResponseDto([]);

        $this->assertSame(0.0, $dto->getMinValue());
    }

    public function testGetMinValueReturnsMinimum(): void
    {
        $items = [
            new TrendItemDto('1', 1709500000, 60, 10.0, 25.0, 50.0),
            new TrendItemDto('2', 1709500100, 120, 5.0, 15.0, 30.0),
        ];

        $dto = new GetTrendResponseDto($items);

        $this->assertSame(5.0, $dto->getMinValue());
    }

    public function testGetMaxValueReturnsZeroForEmptyItems(): void
    {
        $dto = new GetTrendResponseDto([]);

        $this->assertSame(0.0, $dto->getMaxValue());
    }

    public function testGetMaxValueReturnsMaximum(): void
    {
        $items = [
            new TrendItemDto('1', 1709500000, 60, 10.0, 25.0, 50.0),
            new TrendItemDto('2', 1709500100, 120, 5.0, 15.0, 30.0),
        ];

        $dto = new GetTrendResponseDto($items);

        $this->assertSame(50.0, $dto->getMaxValue());
    }
}