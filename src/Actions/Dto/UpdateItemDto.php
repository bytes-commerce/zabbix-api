<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Actions\Dto;

use Webmozart\Assert\Assert;

final readonly class UpdateItemDto
{
    /**
     * @param UpdateSingleItemDto[] $items
     */
    public function __construct(
        public array $items,
    ) {
        Assert::notEmpty($items);
        Assert::allIsInstanceOf($items, UpdateSingleItemDto::class);
    }
}
