<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Enums;

enum EvalTypeEnum: int
{
    case AND_OR = 0;
    case AND = 1;
    case OR = 2;
}
