<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi;

use BytesCommerce\ZabbixApi\Enums\ZabbixAction;

interface ZabbixClientInterface
{
    public function call(ZabbixAction $action, array $params = []): mixed;
}
