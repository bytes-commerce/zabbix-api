<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix;

use BytesCommerce\Zabbix\Actions\AbstractAction;
use BytesCommerce\Zabbix\Enums\ZabbixAction;

final readonly class ZabbixService implements ZabbixServiceInterface
{
    public function __construct(
        private ZabbixClientInterface $zabbixClient,
    ) {
    }

    /**
     * @template T of AbstractAction
     *
     * @param class-string<T> $actionClass
     *
     * @return T
     */
    public function action(string $actionClass): AbstractAction
    {
        if (!is_subclass_of($actionClass, AbstractAction::class)) {
            throw new ZabbixApiException(
                sprintf('Class %s must extend %s', $actionClass, AbstractAction::class),
                -1,
            );
        }

        return new $actionClass($this->zabbixClient);
    }

    public function getApiVersion(): string
    {
        $result = $this->zabbixClient->call(ZabbixAction::APPINFO_VERSION, []);

        if (!is_string($result)) {
            throw new ZabbixApiException('Invalid API version response', -1);
        }

        return $result;
    }

    public function checkHealth(): true
    {
        $this->getApiVersion();

        return true;
    }
}
