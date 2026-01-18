<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Actions;

use BytesCommerce\Zabbix\Actions\Dto\GetEventResponseDto;
use BytesCommerce\Zabbix\Enums\OutputEnum;
use BytesCommerce\Zabbix\Enums\ZabbixAction;
use BytesCommerce\Zabbix\ZabbixApiException;

final class Event extends AbstractAction
{
    public static function getActionPrefix(): string
    {
        return 'event';
    }

    public const int SOURCE_TRIGGER = 0;

    public const int SOURCE_DISCOVERY = 1;

    public const int SOURCE_AUTOREGISTRATION = 2;

    public const int SOURCE_INTERNAL = 3;

    public const int SOURCE_SERVICE = 4;

    public const int ACTION_CLOSE = 1;

    public const int ACTION_ACKNOWLEDGE = 2;

    public const int ACTION_MESSAGE = 4;

    public const int ACTION_SEVERITY = 8;

    public function get(array $params): GetEventResponseDto
    {
        if (!isset($params['output'])) {
            $params['output'] = OutputEnum::EXTEND->value;
        }

        $result = $this->client->call(ZabbixAction::EVENT_GET, $params);

        return GetEventResponseDto::fromArray($result);
    }

    public function acknowledge(array $params): mixed
    {
        if (!isset($params['eventids']) || !is_array($params['eventids'])) {
            throw new ZabbixApiException('Event acknowledge requires eventids array', -1);
        }

        if (!isset($params['action']) || !is_int($params['action'])) {
            throw new ZabbixApiException('Event acknowledge requires action integer', -1);
        }

        return $this->client->call(ZabbixAction::EVENT_ACKNOWLEDGE, $params);
    }
}
