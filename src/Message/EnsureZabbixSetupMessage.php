<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Message;

final readonly class EnsureZabbixSetupMessage
{
    public function __construct(public bool $force = false)
    {
    }
}
