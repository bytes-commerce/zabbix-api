<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Provisioning\Dto;

use BytesCommerce\ZabbixApi\Provisioning\ValueObject\DashboardId;
use BytesCommerce\ZabbixApi\Provisioning\ValueObject\DefinitionHash;
use BytesCommerce\ZabbixApi\Provisioning\ValueObject\ManagedKey;

final readonly class ZabbixDashboard
{
    public function __construct(
        public DashboardId $dashboardId,
        public string $name,
        public ManagedKey $managedKey,
        public DefinitionHash $hash,
        public array $widgets,
    ) {
    }
}
