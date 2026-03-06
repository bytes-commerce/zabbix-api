<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Tests\Actions\Dto;

use BytesCommerce\ZabbixApi\Actions\Dto\TrendItemDto;
use PHPUnit\Framework\TestCase;

final class TrendItemDtoTest extends TestCase
{
    public function testFromArrayCreatesDto(): void
    {
        $data = [
            'itemid' => '12345',
            'clock' => 1709500000,
            'num' => 60,
            'value_min' => '10.5',
            'value_avg' => '25.3',
            'value_max' => '50.0',
        ];

        $dto = TrendItemDto::fromArray($data);

        $this->assertSame('12345', $dto->itemid);
        $this->assertSame(1709500000, $dto->clock);
        $this->assertSame(60, $dto->num);
        $this->assertSame(10.5, $dto->valueMin);
        $this->assertSame(25.3, $dto->valueAvg);
        $this->assertSame(50.0, $dto->valueMax);
    }

    public function testFromArrayHandlesMissingData(): void
    {
        $data = [];

        $dto = TrendItemDto::fromArray($data);

        $this->assertSame('', $dto->itemid);
        $this->assertSame(0, $dto->clock);
        $this->assertSame(0, $dto->num);
        $this->assertSame(0.0, $dto->valueMin);
        $this->assertSame(0.0, $dto->valueAvg);
        $this->assertSame(0.0, $dto->valueMax);
    }

    public function testGetTimestampReturnsDateTimeImmutable(): void
    {
        $data = [
            'itemid' => '12345',
            'clock' => 1709500000,
            'num' => 60,
            'value_min' => '10.5',
            'value_avg' => '25.3',
            'value_max' => '50.0',
        ];

        $dto = TrendItemDto::fromArray($data);
        $timestamp = $dto->getTimestamp();

        $this->assertInstanceOf(\DateTimeImmutable::class, $timestamp);
        $this->assertSame(1709500000, $timestamp->getTimestamp());
    }
}