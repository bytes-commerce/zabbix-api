<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
final readonly class EnsureZabbixSetupMessage
{
    public function __construct(public bool $force = false)
    {
    }
}
