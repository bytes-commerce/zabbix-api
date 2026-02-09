<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Messenger\Handler;

use BytesCommerce\ZabbixApi\Contract\ZabbixSetupInterface;
use BytesCommerce\ZabbixApi\Message\EnsureZabbixSetupMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class EnsureZabbixSetupHandler
{
    public function __construct(private ZabbixSetupInterface $setup)
    {
    }

    public function __invoke(EnsureZabbixSetupMessage $message): void
    {
        $this->setup->ensureAll();
    }
}
