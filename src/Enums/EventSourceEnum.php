<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Enums;

enum EventSourceEnum: int
{
    case TRIGGER = 0;
    case DISCOVERY = 1;
    case AUTOREGISTRATION = 2;
    case INTERNAL = 3;
}
