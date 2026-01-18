<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix;

interface ZabbixServiceInterface
{
    /**
     * @throws ZabbixApiException
     */
    public function getApiVersion(): string;

    /**
     * @throws ZabbixApiException
     */
    public function checkHealth(): bool;
}
