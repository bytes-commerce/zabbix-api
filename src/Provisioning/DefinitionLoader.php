<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Provisioning;

use BytesCommerce\ZabbixApi\Contract\DefinitionLoaderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Yaml\Yaml;
use Webmozart\Assert\Assert;

final readonly class DefinitionLoader implements DefinitionLoaderInterface
{
    public function __construct(
        #[Autowire('%zabbix_api.dashboard_config_path%')]
        private string $configPath,
    ) {
    }

    public function load(string $name): array
    {
        $filePath = \sprintf('%s/%s.yaml', $this->configPath, $name);

        Assert::fileExists($filePath, \sprintf('Dashboard definition file not found: %s', $filePath));

        $data = Yaml::parseFile($filePath);

        Assert::isArray($data);
        Assert::keyExists($data, 'title_template');
        Assert::keyExists($data, 'widgets');

        return $data;
    }

    public function exists(string $name): bool
    {
        $filePath = \sprintf('%s/%s.yaml', $this->configPath, $name);

        return file_exists($filePath);
    }
}
