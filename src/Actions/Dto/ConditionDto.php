<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

use BytesCommerce\ZabbixApi\Enums\ConditionTypeEnum;
use BytesCommerce\ZabbixApi\Enums\OperatorEnum;
use Webmozart\Assert\Assert;

final readonly class ConditionDto
{
    public function __construct(
        public ConditionTypeEnum $conditiontype,
        public OperatorEnum $operator,
        public string $value,
    ) {
        Assert::stringNotEmpty($value);
    }
}
