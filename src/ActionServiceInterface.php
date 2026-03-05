<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi;

interface ActionServiceInterface
{
    /**
     * @param array<string, mixed> $input
     *
     * @throws ZabbixApiException
     */
    public function call(string $actionClass, array $input): mixed;
}
