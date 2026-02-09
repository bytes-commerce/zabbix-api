<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Provisioning;

use BytesCommerce\ZabbixApi\Contract\ZabbixClientWrapperInterface;

final readonly class ZabbixClientFactory
{
    public function __construct(
        private ZabbixClientWrapperInterface $client,
    ) {
    }

    public function create(): ZabbixClientWrapperInterface
    {
        return $this->client;
    }
}
