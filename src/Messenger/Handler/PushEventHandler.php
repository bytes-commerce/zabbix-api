<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Messenger\Handler;

use BytesCommerce\ZabbixApi\Contract\ZabbixSenderInterface;
use BytesCommerce\ZabbixApi\Message\PushEventMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class PushEventHandler
{
    public function __construct(
        private ZabbixSenderInterface $sender,
    ) {
    }

    public function __invoke(PushEventMessage $message): void
    {
        $this->sender->pushEvent(
            key: $message->key,
            payload: $message->payload,
            clock: $message->clock ?? time(),
            correlationId: $message->correlationId,
        );
    }
}
