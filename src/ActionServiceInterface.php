<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi;

interface ActionServiceInterface
{
    /**
     * @throws ZabbixApiException
     */
    public function call(string $actionClass, array $input): mixed;
}
