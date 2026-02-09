<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Service;

use BytesCommerce\ZabbixApi\Contract\ZabbixClientWithApiKeyInterface;
use BytesCommerce\ZabbixApi\Contract\ZabbixClientWrapperInterface;
use BytesCommerce\ZabbixApi\Enums\ZabbixAction;
use Psr\Log\LoggerInterface;
use Throwable;

final readonly class ZabbixClientWrapper implements ZabbixClientWrapperInterface
{
    public function __construct(
        private ZabbixClientWithApiKeyInterface $client,
        private LoggerInterface $logger,
    ) {
    }

    public function call(ZabbixAction $action, array $params = []): mixed
    {
        $this->logger->debug('Zabbix API call', ['method' => $action->value, 'params' => $params]);

        try {
            return $this->client->call($action, $params);
        } catch (Throwable $e) {
            $this->logger->error('Zabbix API call failed', [
                'method' => $action->value,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function getClient(): ZabbixClientWithApiKeyInterface
    {
        return $this->client;
    }
}
