<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Zabbix;

enum SeverityEnum: int
{
    case NOT_CLASSIFIED = 0;
    case INFORMATION = 1;
    case WARNING = 2;
    case AVERAGE = 3;
    case HIGH = 4;
    case DISASTER = 5;
}
