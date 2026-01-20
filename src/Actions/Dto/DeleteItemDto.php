<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

use Webmozart\Assert\Assert;

final readonly class DeleteItemDto
{
    /**
     * @param string[] $itemIds
     */
    public function __construct(
        public array $itemIds,
    ) {
        Assert::notEmpty($itemIds);
        Assert::allStringNotEmpty($itemIds);
    }
}
