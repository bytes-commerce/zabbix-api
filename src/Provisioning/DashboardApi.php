<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Provisioning;

use BytesCommerce\ZabbixApi\Contract\DashboardApiInterface;
use BytesCommerce\ZabbixApi\Enums\ZabbixAction;
use BytesCommerce\ZabbixApi\Provisioning\Dto\DashboardSpec;
use BytesCommerce\ZabbixApi\Provisioning\Dto\HostInfo;
use BytesCommerce\ZabbixApi\Provisioning\Dto\ZabbixDashboard;
use BytesCommerce\ZabbixApi\Provisioning\ValueObject\DashboardId;
use BytesCommerce\ZabbixApi\Provisioning\ValueObject\DefinitionHash;
use BytesCommerce\ZabbixApi\Provisioning\ValueObject\ManagedKey;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Webmozart\Assert\Assert;

final readonly class DashboardApi implements DashboardApiInterface
{
    public function __construct(
        private ZabbixClientFactory $clientFactory,
        private LoggerInterface $logger,
    ) {
    }

    public function findByManagedKey(ManagedKey $key, string $applicationName): ?ZabbixDashboard
    {
        $client = $this->clientFactory->create();

        $result = $client->call(ZabbixAction::DASHBOARD_GET, [
            'filter' => ['name' => $applicationName],
            'selectPages' => 'extend',
            'output' => ['dashboardid', 'name'],
        ]);

        if (!\is_array($result) || \count($result) === 0) {
            return null;
        }

        foreach ($result as $dashboardData) {
            if (!\is_array($dashboardData)) {
                continue;
            }

            $dashboard = $this->parseDashboard($dashboardData);
            if ($dashboard->managedKey->equals($key)) {
                return $dashboard;
            }
        }

        return null;
    }

    public function create(DashboardSpec $spec): DashboardId
    {
        $client = $this->clientFactory->create();

        $widgets = $this->prepareWidgets($spec);

        $result = $client->call(ZabbixAction::DASHBOARD_CREATE, [
            'name' => $spec->name,
            'pages' => [
                [
                    'widgets' => $widgets,
                ],
            ],
        ]);

        Assert::isArray($result);
        Assert::keyExists($result, 'dashboardids');
        Assert::isArray($result['dashboardids']);
        Assert::notEmpty($result['dashboardids']);

        $dashboardId = $result['dashboardids'][0];
        Assert::string($dashboardId);

        $this->logger->info('Zabbix dashboard created', [
            'dashboardId' => $dashboardId,
            'name' => $spec->name,
            'managedKey' => $spec->managedKey->value,
        ]);

        return DashboardId::fromString($dashboardId);
    }

    public function update(DashboardId $id, DashboardSpec $spec): void
    {
        $client = $this->clientFactory->create();

        $widgets = $this->prepareWidgets($spec);

        $result = $client->call(ZabbixAction::DASHBOARD_UPDATE, [
            'dashboardid' => $id->value,
            'name' => $spec->name,
            'pages' => [
                [
                    'widgets' => $widgets,
                ],
            ],
        ]);

        Assert::isArray($result);
        Assert::keyExists($result, 'dashboardids');

        $this->logger->info('Zabbix dashboard updated', [
            'dashboardId' => $id->value,
            'name' => $spec->name,
            'managedKey' => $spec->managedKey->value,
        ]);
    }

    public function get(DashboardId $id): ZabbixDashboard
    {
        $client = $this->clientFactory->create();

        $result = $client->call(ZabbixAction::DASHBOARD_GET, [
            'dashboardids' => [$id->value],
            'selectPages' => 'extend',
            'output' => ['dashboardid', 'name'],
        ]);

        Assert::isArray($result);
        Assert::count($result, 1);

        return $this->parseDashboard($result[0]);
    }

    public function findHostById(string $hostId): ?HostInfo
    {
        $client = $this->clientFactory->create();

        $result = $client->call(ZabbixAction::HOST_GET, [
            'hostids' => [$hostId],
            'output' => ['hostid', 'host', 'name'],
        ]);

        if (!\is_array($result) || \count($result) === 0) {
            return null;
        }

        return HostInfo::fromArray($result[0]);
    }

    public function findHostByName(string $hostName): ?HostInfo
    {
        $client = $this->clientFactory->create();

        $result = $client->call(ZabbixAction::HOST_GET, [
            'filter' => ['host' => $hostName],
            'output' => ['hostid', 'host'],
        ]);

        if (!\is_array($result) || \count($result) === 0) {
            return null;
        }

        return HostInfo::fromArray($result[0]);
    }

    private function parseDashboard(array $data): ZabbixDashboard
    {
        $widgets = $this->extractWidgetsFromPages($data);
        $managedKey = $this->extractManagedKey($widgets);
        $hash = $this->extractHash($widgets);

        return new ZabbixDashboard(
            dashboardId: DashboardId::fromString($data['dashboardid']),
            name: $data['name'],
            managedKey: $managedKey,
            hash: $hash,
            widgets: $widgets,
        );
    }

    private function extractWidgetsFromPages(array $dashboardData): array
    {
        $widgets = [];

        foreach ($dashboardData['pages'] ?? [] as $page) {
            if (!\is_array($page)) {
                continue;
            }

            foreach ($page['widgets'] ?? [] as $widget) {
                if (\is_array($widget)) {
                    $widgets[] = $widget;
                }
            }
        }

        return $widgets;
    }

    private function extractManagedKey(array $widgets): ManagedKey
    {
        foreach ($widgets as $widget) {
            if (!\is_array($widget)) {
                continue;
            }

            if (($widget['type'] ?? '') === 'text' && str_contains((string) ($widget['name'] ?? ''), 'Managed')) {
                foreach ($widget['fields'] ?? [] as $field) {
                    if (!\is_array($field)) {
                        continue;
                    }

                    if (($field['type'] ?? '0') === '1' && str_contains((string) ($field['value'] ?? ''), 'Key:')) {
                        preg_match('/Key:\s*(\S+)/', (string) $field['value'], $matches);
                        if (isset($matches[1])) {
                            return ManagedKey::fromString($matches[1]);
                        }
                    }
                }
            }
        }

        throw new RuntimeException('Managed key not found in dashboard');
    }

    private function extractHash(array $widgets): DefinitionHash
    {
        foreach ($widgets as $widget) {
            if (!\is_array($widget)) {
                continue;
            }

            if (($widget['type'] ?? '') === 'text' && str_contains((string) ($widget['name'] ?? ''), 'Managed')) {
                foreach ($widget['fields'] ?? [] as $field) {
                    if (!\is_array($field)) {
                        continue;
                    }

                    if (($field['type'] ?? '1') === '1' && str_contains((string) ($field['value'] ?? ''), 'Hash:')) {
                        preg_match('/Hash:\s*(\S+)/', (string) $field['value'], $matches);
                        if (isset($matches[1])) {
                            return DefinitionHash::fromString($matches[1]);
                        }
                    }
                }
            }
        }

        throw new RuntimeException('Hash not found in dashboard');
    }

    private function prepareWidgets(DashboardSpec $spec): array
    {
        $widgets = $spec->widgets;

        $managedMarkerWidget = [
            'type' => 'text',
            'name' => 'Managed',
            'x' => 0,
            'y' => 0,
            'width' => 12,
            'height' => 1,
            'fields' => [
                [
                    'type' => 1,
                    'value' => \sprintf(
                        "ManagedBy: ZabbixSetup\nKey: %s\nHash: %s\nUpdatedAt: %s",
                        $spec->managedKey->value,
                        $spec->hash->value,
                        date('Y-m-d H:i:s'),
                    ),
                ],
            ],
        ];

        array_unshift($widgets, $managedMarkerWidget);

        return $widgets;
    }
}
