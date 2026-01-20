<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Enums;

enum ZabbixAction: string
{
    case USER_LOGIN = 'user.login';
    case HISTORY_GET = 'history.get';
    case ACTION_GET = 'action.get';
    case ACTION_CREATE = 'action.create';
    case ACTION_UPDATE = 'action.update';
    case ACTION_DELETE = 'action.delete';
    case ITEM_GET = 'item.get';
    case ITEM_CREATE = 'item.create';
    case ITEM_UPDATE = 'item.update';
    case ITEM_DELETE = 'item.delete';
    case HOST_GET = 'host.get';
    case HOST_CREATE = 'host.create';
    case HOST_UPDATE = 'host.update';
    case HOST_DELETE = 'host.delete';
    case HOST_MASSADD = 'host.massadd';
    case HOST_MASSUPDATE = 'host.massupdate';
    case HOST_MASSREMOVE = 'host.massremove';
    case TRIGGER_GET = 'trigger.get';
    case TRIGGER_CREATE = 'trigger.create';
    case TRIGGER_UPDATE = 'trigger.update';
    case TRIGGER_DELETE = 'trigger.delete';
    case EVENT_GET = 'event.get';
    case EVENT_ACKNOWLEDGE = 'event.acknowledge';
    case ALERT_GET = 'alert.get';
    case AUDITLOG_GET = 'auditlog.get';
    case APPINFO_VERSION = 'appinfo.version';
}
