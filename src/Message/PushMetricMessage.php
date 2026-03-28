<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Message;

use BytesCommerce\ZabbixApi\Contract\MonitoringMessageInterface;

final readonly class PushMetricMessage implements MonitoringMessageInterface
{
    public function __construct(
        public string $key,
        public float|int $value,
        public ?int $clock = null,
        public array $tags = [],
        public string $correlationId = '',
    ) {
    }
}
