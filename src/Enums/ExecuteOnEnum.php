<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Zabbix;

enum ExecuteOnEnum: int
{
    case ZABBIX_AGENT = 0;
    case ZABBIX_SERVER = 1;
    case ZABBIX_SERVER_PROXY = 2;
}
