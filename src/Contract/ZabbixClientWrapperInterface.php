<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Contract;

use BytesCommerce\ZabbixApi\Enums\ZabbixAction;

interface ZabbixClientWrapperInterface
{
    public function call(ZabbixAction $action, array $params = []): mixed;

    public function getClient(): ZabbixClientWithApiKeyInterface;
}
