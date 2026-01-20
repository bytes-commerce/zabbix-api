<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions;

use BytesCommerce\ZabbixApi\Actions\Dto\GetHostResponseDto;
use BytesCommerce\ZabbixApi\Enums\OutputEnum;
use BytesCommerce\ZabbixApi\Enums\ZabbixAction;
use BytesCommerce\ZabbixApi\ZabbixApiException;

final class Host extends AbstractAction
{
    public static function getActionPrefix(): string
    {
        return 'host';
    }

    public function get(array $params): GetHostResponseDto
    {
        if (!isset($params['output'])) {
            $params['output'] = OutputEnum::EXTEND->value;
        }

        $result = $this->client->call(ZabbixAction::HOST_GET, $params);

        return GetHostResponseDto::fromArray($result);
    }

    public function create(array $hosts): mixed
    {
        foreach ($hosts as $host) {
            if (!isset($host['host']) || !isset($host['groups']) || !is_array($host['groups'])) {
                throw new ZabbixApiException('Host creation requires host name and groups array', -1);
            }
        }

        return $this->client->call(ZabbixAction::HOST_CREATE, $hosts);
    }

    public function update(array $hosts): mixed
    {
        foreach ($hosts as $host) {
            if (!isset($host['hostid'])) {
                throw new ZabbixApiException('Host update requires hostid', -1);
            }
        }

        return $this->client->call(ZabbixAction::HOST_UPDATE, $hosts);
    }

    public function delete(array $hostIds): mixed
    {
        return $this->client->call(ZabbixAction::HOST_DELETE, $hostIds);
    }

    public function massAdd(array $params): mixed
    {
        if (!isset($params['hosts']) || !is_array($params['hosts'])) {
            throw new ZabbixApiException('Mass add requires hosts array', -1);
        }

        return $this->client->call(ZabbixAction::HOST_MASSADD, $params);
    }

    public function massUpdate(array $params): mixed
    {
        if (!isset($params['hosts']) || !is_array($params['hosts'])) {
            throw new ZabbixApiException('Mass update requires hosts array', -1);
        }

        return $this->client->call(ZabbixAction::HOST_MASSUPDATE, $params);
    }

    public function massRemove(array $params): mixed
    {
        if (!isset($params['hostids']) || !is_array($params['hostids'])) {
            throw new ZabbixApiException('Mass remove requires hostids array', -1);
        }

        return $this->client->call(ZabbixAction::HOST_MASSREMOVE, $params);
    }
}
