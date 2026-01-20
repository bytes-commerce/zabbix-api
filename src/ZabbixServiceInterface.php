<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi;

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
