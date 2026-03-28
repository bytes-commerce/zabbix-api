<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Message;

use BytesCommerce\ZabbixApi\Contract\MonitoringMessageInterface;

final readonly class EnsureZabbixSetupMessage implements MonitoringMessageInterface
{
    public function __construct(public bool $force = false)
    {
    }
}
