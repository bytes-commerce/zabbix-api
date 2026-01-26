<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Enums;

enum ZabbixAction: string
{
    case ACTION_CREATE = 'action.create';
    case ACTION_DELETE = 'action.delete';
    case ACTION_GET = 'action.get';
    case ACTION_UPDATE = 'action.update';
    case ALERT_GET = 'alert.get';
    case APPINFO_VERSION = 'appinfo.version';
    case AUDITLOG_GET = 'auditlog.get';
    case DASHBOARD_CREATE = 'dashboard.create';
    case DASHBOARD_DELETE = 'dashboard.delete';
    case DASHBOARD_GET = 'dashboard.get';
    case DASHBOARD_UPDATE = 'dashboard.update';
    case EVENT_ACKNOWLEDGE = 'event.acknowledge';
    case EVENT_GET = 'event.get';
    case GRAPH_CREATE = 'graph.create';
    case GRAPH_DELETE = 'graph.delete';
    case GRAPH_GET = 'graph.get';
    case GRAPH_UPDATE = 'graph.update';
    case HISTORY_GET = 'history.get';
    case HISTORY_PUSH = 'history.push';
    case HOSTGROUP_CREATE = 'hostgroup.create';
    case HOSTGROUP_DELETE = 'hostgroup.delete';
    case HOSTGROUP_EXISTS = 'hostgroup.exists';
    case HOSTGROUP_GET = 'hostgroup.get';
    case HOSTGROUP_GETOBJECTS = 'hostgroup.getobjects';
    case HOSTGROUP_ISREADABLE = 'hostgroup.isreadable';
    case HOSTGROUP_ISWRITABLE = 'hostgroup.iswritable';
    case HOSTGROUP_MASSADD = 'hostgroup.massadd';
    case HOSTGROUP_MASSREMOVE = 'hostgroup.massremove';
    case HOSTGROUP_MASSUPDATE = 'hostgroup.massupdate';
    case HOSTGROUP_UPDATE = 'hostgroup.update';
    case HOST_CREATE = 'host.create';
    case HOST_DELETE = 'host.delete';
    case HOST_GET = 'host.get';
    case HOST_MASSADD = 'host.massadd';
    case HOST_MASSREMOVE = 'host.massremove';
    case HOST_MASSUPDATE = 'host.massupdate';
    case HOST_UPDATE = 'host.update';
    case ITEM_CREATE = 'item.create';
    case ITEM_DELETE = 'item.delete';
    case ITEM_GET = 'item.get';
    case ITEM_UPDATE = 'item.update';
    case TRIGGER_CREATE = 'trigger.create';
    case TRIGGER_DELETE = 'trigger.delete';
    case TRIGGER_GET = 'trigger.get';
    case TRIGGER_UPDATE = 'trigger.update';
    case USER_LOGIN = 'user.login';
}
