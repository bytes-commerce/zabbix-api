<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Enums;

enum StatusEnum: int
{
    case ENABLED = 0;
    case DISABLED = 1;
}
