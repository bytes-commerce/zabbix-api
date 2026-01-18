<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Enums;

enum HistoryTypeEnum: int
{
    case NUMERIC_FLOAT = 0;
    case CHARACTER = 1;
    case LOG = 2;
    case NUMERIC_UNSIGNED = 3;
    case TEXT = 4;
    case BINARY = 5;
}
