<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
final readonly class PushEventMessage
{
    public function __construct(
        public string $key,
        public array $payload,
        public ?int $clock = null,
        public string $correlationId = '',
    ) {
    }
}
