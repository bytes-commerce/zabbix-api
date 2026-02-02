<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Contract;

use BytesCommerce\ZabbixApi\Provisioning\Dto\DashboardSpec;
use BytesCommerce\ZabbixApi\Provisioning\Dto\HostInfo;
use BytesCommerce\ZabbixApi\Provisioning\Dto\ZabbixDashboard;
use BytesCommerce\ZabbixApi\Provisioning\ValueObject\DashboardId;
use BytesCommerce\ZabbixApi\Provisioning\ValueObject\ManagedKey;

interface DashboardApiInterface
{
    public function findByManagedKey(ManagedKey $key, string $applicationName): ?ZabbixDashboard;

    public function create(DashboardSpec $spec): DashboardId;

    public function update(DashboardId $id, DashboardSpec $spec): void;

    public function get(DashboardId $id): ZabbixDashboard;

    public function findHostById(string $hostId): ?HostInfo;

    public function findHostByName(string $hostName): ?HostInfo;
}
