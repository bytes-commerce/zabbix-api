<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix;

interface ActionServiceInterface
{
    /**
     * @throws ZabbixApiException
     */
    public function call(string $actionClass, array $input): mixed;
}
