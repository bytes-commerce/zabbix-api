<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix;

use BytesCommerce\Zabbix\Enums\ZabbixAction;

interface ZabbixClientInterface
{
    public function call(ZabbixAction $action, array $params = []): mixed;
}
