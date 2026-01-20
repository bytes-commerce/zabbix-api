<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Zabbix;

enum CommandTypeEnum: int
{
    case CUSTOM_SCRIPT = 0;
    case IPMI = 1;
    case SSH = 2;
    case TELNET = 3;
    case GLOBAL_SCRIPT = 4;
}
