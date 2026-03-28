<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions;

use BytesCommerce\ZabbixApi\Actions\Dto\GetTriggerResponseDto;
use BytesCommerce\ZabbixApi\Enums\OutputEnum;
use BytesCommerce\ZabbixApi\Enums\ZabbixAction;
use BytesCommerce\ZabbixApi\ZabbixApiException;

final class Trigger extends AbstractAction
{
    public static function getActionPrefix(): string
    {
        return 'trigger';
    }

    /**
     * @param array<string, mixed> $params
     */
    public function get(array $params): GetTriggerResponseDto
    {
        if (!isset($params['output'])) {
            $params['output'] = OutputEnum::EXTEND->value;
        }

        $result = $this->client->call(ZabbixAction::TRIGGER_GET, $params);

        if (!is_array($result)) {
            throw new ZabbixApiException('Invalid response from Zabbix API: expected array', -1);
        }

        return GetTriggerResponseDto::fromArray($result);
    }

    /**
     * @param array<int, array<string, mixed>> $triggers
     */
    public function create(array $triggers): mixed
    {
        foreach ($triggers as $trigger) {
            if (!is_array($trigger) || !isset($trigger['description']) || !isset($trigger['expression'])) {
                throw new ZabbixApiException('Trigger creation requires description and expression', -1);
            }
        }

        return $this->client->call(ZabbixAction::TRIGGER_CREATE, $triggers);
    }

    /**
     * @param array<int, array<string, mixed>> $triggers
     */
    public function update(array $triggers): mixed
    {
        foreach ($triggers as $trigger) {
            if (!is_array($trigger) || !isset($trigger['triggerid'])) {
                throw new ZabbixApiException('Trigger update requires triggerid', -1);
            }
        }

        return $this->client->call(ZabbixAction::TRIGGER_UPDATE, $triggers);
    }

    /**
     * @param array<int, string> $triggerIds
     */
    public function delete(array $triggerIds): mixed
    {
        return $this->client->call(ZabbixAction::TRIGGER_DELETE, $triggerIds);
    }
}
