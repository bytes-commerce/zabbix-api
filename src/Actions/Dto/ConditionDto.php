<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Actions\Dto;

use BytesCommerce\Zabbix\Enums\ConditionTypeEnum;
use BytesCommerce\Zabbix\Enums\OperatorEnum;
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
