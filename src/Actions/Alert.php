<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Actions;

use BytesCommerce\Zabbix\Actions\Dto\GetAlertResponseDto;
use BytesCommerce\Zabbix\Enums\OutputEnum;
use BytesCommerce\Zabbix\Enums\ZabbixAction;
use DateTimeInterface;

final class Alert extends AbstractAction
{
    public static function getActionPrefix(): string
    {
        return 'alert';
    }

    public function get(array $params): GetAlertResponseDto
    {
        $processedParams = $this->processParams($params);

        $result = $this->client->call(ZabbixAction::ALERT_GET, $processedParams);

        return GetAlertResponseDto::fromArray($result);
    }

    private function processParams(array $params): array
    {
        if (!isset($params['output'])) {
            $params['output'] = OutputEnum::EXTEND->value;
        }

        if (isset($params['time_from']) && $params['time_from'] instanceof DateTimeInterface) {
            $params['time_from'] = $params['time_from']->getTimestamp();
        }

        if (isset($params['time_till']) && $params['time_till'] instanceof DateTimeInterface) {
            $params['time_till'] = $params['time_till']->getTimestamp();
        }

        return $params;
    }
}
