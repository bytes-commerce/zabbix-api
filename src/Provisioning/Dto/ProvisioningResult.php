<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Provisioning\Dto;

use BytesCommerce\ZabbixApi\Provisioning\ValueObject\DashboardId;

enum ProvisioningStatus: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case UNCHANGED = 'unchanged';
}

final readonly class ProvisioningResult
{
    public function __construct(
        public ProvisioningStatus $status,
        public ?DashboardId $dashboardId,
        public string $message,
    ) {
    }

    public static function created(DashboardId $dashboardId): self
    {
        return new self(
            status: ProvisioningStatus::CREATED,
            dashboardId: $dashboardId,
            message: \sprintf('Dashboard created with ID %s', $dashboardId->value),
        );
    }

    public static function updated(DashboardId $dashboardId): self
    {
        return new self(
            status: ProvisioningStatus::UPDATED,
            dashboardId: $dashboardId,
            message: \sprintf('Dashboard updated with ID %s', $dashboardId->value),
        );
    }

    public static function unchanged(DashboardId $dashboardId): self
    {
        return new self(
            status: ProvisioningStatus::UNCHANGED,
            dashboardId: $dashboardId,
            message: \sprintf('Dashboard unchanged with ID %s', $dashboardId->value),
        );
    }

    public function isCreated(): bool
    {
        return $this->status === ProvisioningStatus::CREATED;
    }

    public function isUpdated(): bool
    {
        return $this->status === ProvisioningStatus::UPDATED;
    }

    public function isUnchanged(): bool
    {
        return $this->status === ProvisioningStatus::UNCHANGED;
    }
}
