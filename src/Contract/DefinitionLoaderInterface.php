<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Contract;

interface DefinitionLoaderInterface
{
    public function load(string $name): array;

    public function exists(string $name): bool;
}
