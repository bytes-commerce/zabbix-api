<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Contract;

interface ZabbixSetupInterface
{
    public function ensureFast(): void;

    public function ensureAll(): void;

    public function ensureHost(): string;
}
