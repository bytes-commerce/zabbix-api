<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

use Webmozart\Assert\Assert;

final readonly class DeleteActionDto
{
    /**
     * @param string[] $actionIds
     */
    public function __construct(
        public array $actionIds,
    ) {
        Assert::notEmpty($actionIds);
        Assert::allStringNotEmpty($actionIds);
    }
}
