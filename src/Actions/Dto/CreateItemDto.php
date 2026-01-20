<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

use Webmozart\Assert\Assert;

final readonly class CreateItemDto
{
    /**
     * @param CreateSingleItemDto[] $items
     */
    public function __construct(
        public array $items,
    ) {
        Assert::notEmpty($items);
        Assert::allIsInstanceOf($items, CreateSingleItemDto::class);
    }
}
