<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Contract;

use BytesCommerce\ZabbixApi\Provisioning\Dto\ProvisioningResult;

interface DashboardProvisionerInterface
{
    public function provisionForHost(
        string $hostIdentifier,
        string $dashboardName = 'ops_overview',
        bool $dryRun = false,
    ): ProvisioningResult;
}
