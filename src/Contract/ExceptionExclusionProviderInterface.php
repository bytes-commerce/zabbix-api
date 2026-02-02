<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Contract;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('zabbix.exception_exclusion_provider')]
interface ExceptionExclusionProviderInterface
{
    /**
     * @return list<class-string<\Throwable>>
     */
    public function getExcludedExceptionClasses(): array;
}
