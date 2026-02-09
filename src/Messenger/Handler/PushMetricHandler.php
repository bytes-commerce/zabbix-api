<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Messenger\Handler;

use BytesCommerce\ZabbixApi\Contract\ZabbixSenderInterface;
use BytesCommerce\ZabbixApi\Message\PushMetricMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class PushMetricHandler
{
    public function __construct(private ZabbixSenderInterface $sender)
    {
    }

    public function __invoke(PushMetricMessage $message): void
    {
        $this->sender->pushNumeric(
            key: $message->key,
            value: $message->value,
            clock: $message->clock ?? time(),
            tags: $message->tags,
            correlationId: $message->correlationId,
        );
    }
}
