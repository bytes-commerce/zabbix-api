<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Contract;

interface ZabbixNamingProviderInterface
{
    public function getAppName(): string;

    public function getHostName(): string;

    public function getCleanHostName(): string;

    public function getHostGroup(): string;

    public function getDashboardName(): string;

    public function getDashboardPrefix(): string;

    public function getItemKey(string $suffix): string;

    public function getEnvLabel(): string;
}
