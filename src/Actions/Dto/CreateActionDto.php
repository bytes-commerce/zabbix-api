<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Actions\Dto;

use Webmozart\Assert\Assert;

final readonly class CreateActionDto
{
    /**
     * @param CreateSingleActionDto[] $actions
     */
    public function __construct(
        public array $actions,
    ) {
        Assert::notEmpty($actions);
        Assert::allIsInstanceOf($actions, CreateSingleActionDto::class);
    }
}
