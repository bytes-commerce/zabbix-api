<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

use BytesCommerce\ZabbixApi\Enums\EvalTypeEnum;
use Webmozart\Assert\Assert;

final readonly class FilterDto
{
    /**
     * @param ConditionDto[] $conditions
     */
    public function __construct(
        public EvalTypeEnum $evaltype,
        public array $conditions,
    ) {
        Assert::notEmpty($conditions);
        Assert::allIsInstanceOf($conditions, ConditionDto::class);
    }
}
