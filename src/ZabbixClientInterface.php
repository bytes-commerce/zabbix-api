<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi;

use BytesCommerce\ZabbixApi\Enums\ZabbixAction;

interface ZabbixClientInterface
{
    /**
     * @param array<int|string, mixed> $params
     */
    public function call(ZabbixAction $action, array $params = []): mixed;
}
