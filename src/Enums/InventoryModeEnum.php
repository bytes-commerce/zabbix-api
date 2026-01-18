<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Zabbix;

enum InventoryModeEnum: int
{
    case DISABLED = -1;
    case MANUAL = 0;
    case AUTOMATIC = 1;
}
