<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Enums;

enum ConditionTypeEnum: int
{
    case HOST_GROUP = 0;
    case HOST = 1;
    case TRIGGER = 2;
    case TRIGGER_NAME = 3;
    case TRIGGER_SEVERITY = 4;
    case TRIGGER_VALUE = 5;
    case TIME_PERIOD = 6;
    case HOST_IP = 7;
    case DISCOVERED_SERVICE_TYPE = 8;
    case DISCOVERED_SERVICE_PORT = 9;
    case DISCOVERY_STATUS = 10;
    case UPTIME_DOWNTIME = 11;
    case RECEIVED_VALUE = 12;
    case HOST_PROXY = 13;
    case DISCOVERY_RULE = 14;
    case DISCOVERY_CHECK = 15;
    case PROXY = 16;
    case DISCOVERY_OBJECT = 17;
}
