<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Contract;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('zabbix.entity_exclusion_provider')]
interface EntityExclusionProviderInterface
{
    /**
     * @return list<class-string>
     */
    public function getExcludedEntityClasses(): array;
}
