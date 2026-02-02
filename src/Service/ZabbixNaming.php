<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Service;

use BytesCommerce\ZabbixApi\Contract\ZabbixNamingProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\String\Slugger\SluggerInterface;

final readonly class ZabbixNaming implements ZabbixNamingProviderInterface
{
    private const string DASHBOARD_PREFIX = '[APP]';

    public function __construct(
        private SluggerInterface $slugger,
        #[Autowire('%kernel.environment%')]
        private string $appEnv,
        #[Autowire('%zabbix_api.app_name%')]
        private string $appName,
        #[Autowire('%zabbix_api.host_group%')]
        private string $hostGroup,
    ) {
    }

    public function getEnvLabel(): string
    {
        return strtoupper($this->appEnv);
    }

    public function getAppName(): string
    {
        return \sprintf('%s [%s] %s', self::DASHBOARD_PREFIX, $this->getEnvLabel(), $this->appName);
    }

    public function getHostGroup(): string
    {
        return $this->hostGroup;
    }

    public function getDashboardPrefix(): string
    {
        return \sprintf('%s Dashboard', self::DASHBOARD_PREFIX);
    }

    public function getHostName(): string
    {
        return $this->getAppName();
    }

    public function getDashboardName(): string
    {
        return \sprintf('%s — %s', $this->getDashboardPrefix(), $this->getAppName());
    }

    public function getItemKey(string $suffix): string
    {
        return \sprintf('symfony.%s', $suffix);
    }

    public function getCleanHostName(): string
    {
        return strtolower($this->slugger->slug($this->getHostName())->toString());
    }
}
