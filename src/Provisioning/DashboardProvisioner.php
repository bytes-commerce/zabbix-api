<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Provisioning;

use BytesCommerce\ZabbixApi\Contract\DashboardApiInterface;
use BytesCommerce\ZabbixApi\Contract\DefinitionLoaderInterface;
use BytesCommerce\ZabbixApi\Contract\ZabbixNamingProviderInterface;
use BytesCommerce\ZabbixApi\Provisioning\Dto\DashboardSpec;
use BytesCommerce\ZabbixApi\Provisioning\Dto\HostInfo;
use BytesCommerce\ZabbixApi\Provisioning\Dto\ProvisioningResult;
use BytesCommerce\ZabbixApi\Provisioning\Dto\ProvisioningStatus;
use BytesCommerce\ZabbixApi\Provisioning\Dto\ZabbixDashboard;
use BytesCommerce\ZabbixApi\Provisioning\ValueObject\ManagedKey;
use BytesCommerce\ZabbixApi\Contract\DashboardProvisionerInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

final readonly class DashboardProvisioner implements DashboardProvisionerInterface
{
    public function __construct(
        private DashboardApiInterface $dashboardApi,
        private DefinitionLoaderInterface $definitionLoader,
        private SpecRenderer $specRenderer,
        private SpecHasher $specHasher,
        private ZabbixNamingProviderInterface $naming,
        private LoggerInterface $logger,
    ) {
    }

    public function provisionForHost(
        string $hostIdentifier,
        string $dashboardName = 'ops_overview',
        bool $dryRun = false,
    ): ProvisioningResult {
        $this->logger->info('Starting dashboard provisioning', [
            'hostIdentifier' => $hostIdentifier,
            'dashboardName' => $dashboardName,
            'dryRun' => $dryRun,
        ]);

        $host = $this->resolveHost($hostIdentifier);

        $managedKey = ManagedKey::fromComponents(
            $this->naming->getEnvLabel(),
            $this->naming->getEnvLabel(),
            $host->hostId,
        );

        $definition = $this->definitionLoader->load($dashboardName);

        $hash = $this->specHasher->hash($definition);

        $titleTemplate = $definition['title_template'] ?? '';
        $widgetsDef = $definition['widgets'] ?? [];

        if (!is_string($titleTemplate)) {
            throw new RuntimeException('Invalid definition: title_template must be a string');
        }

        if (!is_array($widgetsDef)) {
            throw new RuntimeException('Invalid definition: widgets must be an array');
        }

        $title = $this->specRenderer->renderTitleTemplate($titleTemplate, $host);
        $widgets = $this->specRenderer->renderWidgets($widgetsDef, $host);

        $spec = new DashboardSpec(
            name: $title,
            managedKey: $managedKey,
            hash: $hash,
            widgets: $widgets,
        );

        $existingDashboard = $this->dashboardApi->findByManagedKey($managedKey, $title);

        if ($existingDashboard === null) {
            return $this->createDashboard($spec, $dryRun);
        }

        return $this->updateDashboardIfNeeded($existingDashboard, $spec, $dryRun);
    }

    private function resolveHost(string $hostIdentifier): HostInfo
    {
        $host = $this->dashboardApi->findHostById($hostIdentifier);

        if ($host !== null) {
            return $host;
        }

        $host = $this->dashboardApi->findHostByName($hostIdentifier);

        if ($host !== null) {
            return $host;
        }

        throw new RuntimeException(\sprintf('Host not found: %s', $hostIdentifier));
    }

    private function createDashboard(DashboardSpec $spec, bool $dryRun): ProvisioningResult
    {
        if ($dryRun) {
            $this->logger->info('Dry run: Would create dashboard', [
                'name' => $spec->name,
                'managedKey' => $spec->managedKey->value,
                'hash' => $spec->hash->value,
            ]);

            return new ProvisioningResult(
                status: ProvisioningStatus::CREATED,
                dashboardId: null,
                message: \sprintf('Dry run: Would create dashboard "%s"', $spec->name),
            );
        }

        $dashboardId = $this->dashboardApi->create($spec);

        $this->logger->info('Dashboard created successfully', [
            'dashboardId' => $dashboardId->value,
            'name' => $spec->name,
            'managedKey' => $spec->managedKey->value,
        ]);

        return ProvisioningResult::created($dashboardId);
    }

    private function updateDashboardIfNeeded(ZabbixDashboard $existingDashboard, DashboardSpec $spec, bool $dryRun): ProvisioningResult
    {
        if ($existingDashboard->hash->equals($spec->hash)) {
            $this->logger->info('Dashboard unchanged', [
                'dashboardId' => $existingDashboard->dashboardId->value,
                'name' => $existingDashboard->name,
                'hash' => $existingDashboard->hash->value,
            ]);

            return ProvisioningResult::unchanged($existingDashboard->dashboardId);
        }

        if ($dryRun) {
            $this->logger->info('Dry run: Would update dashboard', [
                'dashboardId' => $existingDashboard->dashboardId->value,
                'name' => $spec->name,
                'oldHash' => $existingDashboard->hash->value,
                'newHash' => $spec->hash->value,
            ]);

            return new ProvisioningResult(
                status: ProvisioningStatus::UPDATED,
                dashboardId: $existingDashboard->dashboardId,
                message: \sprintf('Dry run: Would update dashboard "%s"', $spec->name),
            );
        }

        $this->dashboardApi->update($existingDashboard->dashboardId, $spec);

        $this->logger->info('Dashboard updated successfully', [
            'dashboardId' => $existingDashboard->dashboardId->value,
            'name' => $spec->name,
            'oldHash' => $existingDashboard->hash->value,
            'newHash' => $spec->hash->value,
        ]);

        return ProvisioningResult::updated($existingDashboard->dashboardId);
    }
}
