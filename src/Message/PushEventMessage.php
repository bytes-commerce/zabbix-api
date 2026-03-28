<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Message;

use BytesCommerce\ZabbixApi\Contract\MonitoringMessageInterface;

final readonly class PushEventMessage implements MonitoringMessageInterface
{
    public function __construct(
        public string $key,
        public array $payload,
        public ?int $clock = null,
        public string $correlationId = '',
    ) {
    }
}
