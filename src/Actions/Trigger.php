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

    public function get(array $params): GetTriggerResponseDto
    {
        if (!isset($params['output'])) {
            $params['output'] = OutputEnum::EXTEND->value;
        }

        $result = $this->client->call(ZabbixAction::TRIGGER_GET, $params);

        return GetTriggerResponseDto::fromArray($result);
    }

    public function create(array $triggers): mixed
    {
        foreach ($triggers as $trigger) {
            if (!isset($trigger['description']) || !isset($trigger['expression'])) {
                throw new ZabbixApiException('Trigger creation requires description and expression', -1);
            }
        }

        return $this->client->call(ZabbixAction::TRIGGER_CREATE, $triggers);
    }

    public function update(array $triggers): mixed
    {
        foreach ($triggers as $trigger) {
            if (!isset($trigger['triggerid'])) {
                throw new ZabbixApiException('Trigger update requires triggerid', -1);
            }
        }

        return $this->client->call(ZabbixAction::TRIGGER_UPDATE, $triggers);
    }

    public function delete(array $triggerIds): mixed
    {
        return $this->client->call(ZabbixAction::TRIGGER_DELETE, $triggerIds);
    }
}
