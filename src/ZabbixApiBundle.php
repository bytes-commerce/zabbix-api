<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix;

use BytesCommerce\Zabbix\DependencyInjection\ZabbixApiExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class ZabbixApiBundle extends Bundle
{
    public function getContainerExtension(): ZabbixApiExtension
    {
        return new ZabbixApiExtension();
    }
}
