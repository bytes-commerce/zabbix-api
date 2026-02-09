<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
final readonly class PushMetricMessage
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
