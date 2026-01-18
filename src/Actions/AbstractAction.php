<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Actions;

use BytesCommerce\Zabbix\ZabbixClientInterface;

abstract class AbstractAction
{
    public function __construct(
        protected readonly ZabbixClientInterface $client,
    ) {
    }

    abstract public static function getActionPrefix(): string;
}
