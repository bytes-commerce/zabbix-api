<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Enums;

enum OperatorEnum: int
{
    case EQUAL = 0;
    case NOT_EQUAL = 1;
    case LIKE = 2;
    case NOT_LIKE = 3;
    case IN = 4;
    case GREATER_EQUAL = 5;
    case LESS_EQUAL = 6;
    case NOT_IN = 7;
}
