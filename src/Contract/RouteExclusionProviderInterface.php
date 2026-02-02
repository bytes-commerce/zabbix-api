<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Contract;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('zabbix.route_exclusion_provider')]
interface RouteExclusionProviderInterface
{
    /**
     * @return list<string>
     */
    public function getExcludedRoutes(): array;
}
