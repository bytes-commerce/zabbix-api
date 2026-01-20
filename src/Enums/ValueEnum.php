<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Zabbix;

enum ValueEnum: int
{
    case OK = 0;
    case PROBLEM = 1;
}
