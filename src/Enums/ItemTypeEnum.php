<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Enums;

enum ItemTypeEnum: int
{
    case ZABBIX_AGENT = 0;
    case SNMPV1_AGENT = 1;
    case ZABBIX_TRAPPER = 2;
    case SIMPLE_CHECK = 3;
    case SNMPV2_AGENT = 4;
    case ZABBIX_INTERNAL = 5;
    case SNMPV3_AGENT = 6;
    case ZABBIX_AGENT_ACTIVE = 7;
    case ZABBIX_AGGREGATE = 8;
    case WEB_ITEM = 9;
    case EXTERNAL_CHECK = 10;
    case DATABASE_MONITOR = 11;
    case IPMI_AGENT = 12;
    case SSH_AGENT = 13;
    case TELNET_AGENT = 14;
    case CALCULATED = 15;
    case JMX_AGENT = 16;
    case SNMP_TRAP = 17;
    case DEPENDENT_ITEM = 18;
    case HTTP_AGENT = 19;
    case SNMP_AGENT = 20;
    case SCRIPT = 21;
}
