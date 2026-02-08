<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi;

use BytesCommerce\ZabbixApi\Actions\AbstractAction;

interface ZabbixServiceInterface
{
    /**
     * @template T of AbstractAction
     *
     * @param class-string<T> $actionClass
     *
     * @return T
     *
     * @throws ZabbixApiException
     */
    public function action(string $actionClass): AbstractAction;

    /**
     * @throws ZabbixApiException
     */
    public function getApiVersion(): string;

    /**
     * @throws ZabbixApiException
     */
    public function checkHealth(): bool;
}
