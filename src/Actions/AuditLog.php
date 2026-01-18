<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Actions;

use BytesCommerce\Zabbix\Actions\Dto\GetAuditLogResponseDto;
use BytesCommerce\Zabbix\Enums\OutputEnum;
use BytesCommerce\Zabbix\Enums\ZabbixAction;
use DateTimeInterface;

final class AuditLog extends AbstractAction
{
    public static function getActionPrefix(): string
    {
        return 'auditlog';
    }

    public function get(array $params): GetAuditLogResponseDto
    {
        $processedParams = $this->processParams($params);

        $result = $this->client->call(ZabbixAction::AUDITLOG_GET, $processedParams);

        return GetAuditLogResponseDto::fromArray($result);
    }

    private function processParams(array $params): array
    {
        if (!isset($params['output'])) {
            $params['output'] = OutputEnum::EXTEND->value;
        }

        if (!isset($params['selectDetails'])) {
            $params['selectDetails'] = OutputEnum::EXTEND->value;
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
