<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions;

use BytesCommerce\ZabbixApi\Actions\Dto\CreateDashboardDto;
use BytesCommerce\ZabbixApi\Actions\Dto\CreateDashboardResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\DeleteDashboardDto;
use BytesCommerce\ZabbixApi\Actions\Dto\DeleteDashboardResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\GetDashboardDto;
use BytesCommerce\ZabbixApi\Actions\Dto\GetDashboardResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\UpdateDashboardDto;
use BytesCommerce\ZabbixApi\Actions\Dto\UpdateDashboardResponseDto;
use BytesCommerce\ZabbixApi\Enums\OutputEnum;
use BytesCommerce\ZabbixApi\Enums\ZabbixAction;
use Webmozart\Assert\Assert;

final class Dashboard extends AbstractAction
{
    public static function getActionPrefix(): string
    {
        return 'dashboard';
    }

    public function get(GetDashboardDto $dto): GetDashboardResponseDto
    {
        $params = array_filter((array) $dto, fn ($v) => $v !== null);
        if (!isset($params['output'])) {
            $params['output'] = OutputEnum::EXTEND->value;
        }

        $result = $this->client->call(ZabbixAction::DASHBOARD_GET, $params);

        return GetDashboardResponseDto::fromArray(is_array($result) ? $result : []);
    }

    public function create(CreateDashboardDto $dto): CreateDashboardResponseDto
    {
        $params = [];
        foreach ($dto->dashboards as $dashboard) {
            $params[] = $this->mapCreateDashboard($dashboard);
        }

        $result = $this->client->call(ZabbixAction::DASHBOARD_CREATE, $params);
        Assert::isArray($result);
        Assert::keyExists($result, 'dashboardids');
        Assert::isList($result['dashboardids']);

        /** @var list<string> $dashboardids */
        $dashboardids = $result['dashboardids'];

        return new CreateDashboardResponseDto($dashboardids);
    }

    public function update(UpdateDashboardDto $dto): UpdateDashboardResponseDto
    {
        $params = [];
        foreach ($dto->dashboards as $dashboard) {
            $params[] = $this->mapUpdateDashboard($dashboard);
        }

        $result = $this->client->call(ZabbixAction::DASHBOARD_UPDATE, $params);
        Assert::isArray($result);
        Assert::keyExists($result, 'dashboardids');
        Assert::isList($result['dashboardids']);

        /** @var list<string> $dashboardids */
        $dashboardids = $result['dashboardids'];

        return new UpdateDashboardResponseDto($dashboardids);
    }

    public function delete(DeleteDashboardDto $dto): DeleteDashboardResponseDto
    {
        $this->client->call(ZabbixAction::DASHBOARD_DELETE, $dto->dashboardIds);

        return new DeleteDashboardResponseDto();
    }

    /**
     * @param array<string, mixed> $dashboard
     * @return array<string, mixed>
     */
    private function mapCreateDashboard(array $dashboard): array
    {
        $data = [
            'name' => $dashboard['name'],
        ];

        if (isset($dashboard['pages'])) {
            $data['pages'] = $dashboard['pages'];
        }

        if (isset($dashboard['private'])) {
            $data['private'] = $dashboard['private'];
        }

        if (isset($dashboard['userid'])) {
            $data['userid'] = $dashboard['userid'];
        }

        if (isset($dashboard['display_period'])) {
            $data['display_period'] = $dashboard['display_period'];
        }

        if (isset($dashboard['auto_start'])) {
            $data['auto_start'] = $dashboard['auto_start'];
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $dashboard
     * @return array<string, mixed>
     */
    private function mapUpdateDashboard(array $dashboard): array
    {
        $data = [
            'dashboardid' => $dashboard['dashboardid'],
        ];

        if (isset($dashboard['name'])) {
            $data['name'] = $dashboard['name'];
        }

        if (isset($dashboard['pages'])) {
            $data['pages'] = $dashboard['pages'];
        }

        if (isset($dashboard['private'])) {
            $data['private'] = $dashboard['private'];
        }

        if (isset($dashboard['userid'])) {
            $data['userid'] = $dashboard['userid'];
        }

        if (isset($dashboard['display_period'])) {
            $data['display_period'] = $dashboard['display_period'];
        }

        if (isset($dashboard['auto_start'])) {
            $data['auto_start'] = $dashboard['auto_start'];
        }

        return $data;
    }
}
