<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Zabbix;

enum OperationTypeEnum: int
{
    case SEND_MESSAGE = 0;
    case REMOTE_COMMAND = 1;
    case ADD_HOST = 2;
    case REMOVE_HOST = 3;
    case ADD_TO_HOST_GROUP = 4;
    case REMOVE_FROM_HOST_GROUP = 5;
    case LINK_TO_TEMPLATE = 6;
    case UNLINK_FROM_TEMPLATE = 7;
    case ENABLE_HOST = 8;
    case DISABLE_HOST = 9;
    case SET_HOST_INVENTORY_MODE = 10;
}
