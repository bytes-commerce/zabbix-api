<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Tests;

use BytesCommerce\ZabbixApi\Actions\Dto\CreateDashboardDto;
use BytesCommerce\ZabbixApi\Actions\Dto\CreateDashboardResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\DashboardDto;
use BytesCommerce\ZabbixApi\Actions\Dto\DashboardPageDto;
use BytesCommerce\ZabbixApi\Actions\Dto\DashboardWidgetDto;
use BytesCommerce\ZabbixApi\Actions\Dto\DeleteDashboardDto;
use BytesCommerce\ZabbixApi\Actions\Dto\DeleteDashboardResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\GetDashboardDto;
use BytesCommerce\ZabbixApi\Actions\Dto\GetDashboardResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\UpdateDashboardDto;
use BytesCommerce\ZabbixApi\Actions\Dto\UpdateDashboardResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dashboard;
use BytesCommerce\ZabbixApi\Enums\ZabbixAction;
use BytesCommerce\ZabbixApi\ZabbixClientInterface;
use PHPUnit\Framework\TestCase;

final class DashboardTest extends TestCase
{
    private ZabbixClientInterface $zabbixClient;

    private Dashboard $dashboard;

    protected function setUp(): void
    {
        $this->zabbixClient = $this->createMock(ZabbixClientInterface::class);
        $this->dashboard = new Dashboard($this->zabbixClient);
    }

    public function testGetWithDefaultParameters(): void
    {
        $dto = new GetDashboardDto(
            dashboardids: ['12345'],
            filter: null,
            output: null,
            selectPages: null,
            selectUsers: null,
            selectUserGroups: null,
            sortfield: null,
            sortorder: null,
            limit: null,
            preservekeys: null,
        );
        $expectedParams = [
            'dashboardids' => ['12345'],
            'output' => 'extend',
        ];
        $expectedResult = [
            [
                'dashboardid' => '12345',
                'name' => 'Test Dashboard',
                'pages' => [],
            ],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::DASHBOARD_GET, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->dashboard->get($dto);

        self::assertInstanceOf(GetDashboardResponseDto::class, $result);
        self::assertCount(1, $result->getDashboards());
    }

    public function testGetWithCustomParameters(): void
    {
        $dto = new GetDashboardDto(
            dashboardids: ['12345', '67890'],
            filter: ['name' => 'Test Dashboard'],
            output: 'extend',
            selectPages: true,
            selectUsers: true,
            selectUserGroups: true,
            sortfield: 1,
            sortorder: 'DESC',
            limit: 10,
            preservekeys: true,
        );
        $expectedParams = [
            'dashboardids' => ['12345', '67890'],
            'filter' => ['name' => 'Test Dashboard'],
            'output' => 'extend',
            'selectPages' => true,
            'selectUsers' => true,
            'selectUserGroups' => true,
            'sortfield' => 1,
            'sortorder' => 'DESC',
            'limit' => 10,
            'preservekeys' => true,
        ];
        $expectedResult = [
            [
                'dashboardid' => '12345',
                'name' => 'Test Dashboard',
            ],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::DASHBOARD_GET, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->dashboard->get($dto);

        self::assertInstanceOf(GetDashboardResponseDto::class, $result);
    }

    public function testCreateValid(): void
    {
        $dashboards = [
            [
                'name' => 'Test Dashboard',
                'pages' => [
                    [
                        'name' => 'Page 1',
                        'widgets' => [],
                    ],
                ],
            ],
        ];
        $dto = new CreateDashboardDto($dashboards);
        $expectedParams = [
            [
                'name' => 'Test Dashboard',
                'pages' => [
                    [
                        'name' => 'Page 1',
                        'widgets' => [],
                    ],
                ],
            ],
        ];
        $expectedResult = [
            'dashboardids' => ['12345'],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::DASHBOARD_CREATE, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->dashboard->create($dto);

        self::assertInstanceOf(CreateDashboardResponseDto::class, $result);
        self::assertSame(['12345'], $result->getDashboardids());
    }

    public function testUpdateValid(): void
    {
        $dashboards = [
            [
                'dashboardid' => '12345',
                'name' => 'Updated Dashboard',
            ],
        ];
        $dto = new UpdateDashboardDto($dashboards);
        $expectedParams = [
            [
                'dashboardid' => '12345',
                'name' => 'Updated Dashboard',
            ],
        ];
        $expectedResult = [
            'dashboardids' => ['12345'],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::DASHBOARD_UPDATE, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->dashboard->update($dto);

        self::assertInstanceOf(UpdateDashboardResponseDto::class, $result);
        self::assertSame(['12345'], $result->getDashboardids());
    }

    public function testDelete(): void
    {
        $dto = new DeleteDashboardDto(['12345', '67890']);
        $expectedParams = ['12345', '67890'];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::DASHBOARD_DELETE, $expectedParams);

        $result = $this->dashboard->delete($dto);

        self::assertInstanceOf(DeleteDashboardResponseDto::class, $result);
    }

    public function testDashboardDtoFromArray(): void
    {
        $data = [
            'dashboardid' => '12345',
            'name' => 'Test Dashboard',
            'private' => 0,
            'userid' => '1',
            'display_period' => 3600,
            'auto_start' => 1,
            'pages' => [
                [
                    'name' => 'Page 1',
                    'display_period' => 1800,
                    'sortorder' => 1,
                    'widgets' => [],
                ],
            ],
        ];

        $dto = DashboardDto::fromArray($data);

        self::assertSame('12345', $dto->getDashboardid());
        self::assertSame('Test Dashboard', $dto->getName());
        self::assertSame(0, $dto->getPrivate());
        self::assertSame('1', $dto->getUserid());
        self::assertSame(3600, $dto->getDisplayPeriod());
        self::assertSame(1, $dto->getAutoStart());
        self::assertCount(1, $dto->getPages());
    }

    public function testDashboardDtoFromArrayWithNulls(): void
    {
        $data = [
            'dashboardid' => '12345',
            'name' => 'Test Dashboard',
            'pages' => [],
        ];

        $dto = DashboardDto::fromArray($data);

        self::assertSame('12345', $dto->getDashboardid());
        self::assertSame('Test Dashboard', $dto->getName());
        self::assertNull($dto->getPrivate());
        self::assertNull($dto->getUserid());
        self::assertNull($dto->getDisplayPeriod());
        self::assertNull($dto->getAutoStart());
        self::assertCount(0, $dto->getPages());
    }

    public function testDashboardPageDtoFromArray(): void
    {
        $data = [
            'name' => 'Page 1',
            'display_period' => 1800,
            'sortorder' => 1,
            'widgets' => [
                [
                    'type' => 'graph',
                    'name' => 'CPU Graph',
                    'x' => 0,
                    'y' => 0,
                    'width' => 12,
                    'height' => 5,
                    'fields' => [],
                    'view_mode' => '0',
                ],
            ],
        ];

        $dto = DashboardPageDto::fromArray($data);

        self::assertSame('Page 1', $dto->getName());
        self::assertSame(1800, $dto->getDisplayPeriod());
        self::assertSame(1, $dto->getSortorder());
        self::assertCount(1, $dto->getWidgets());
    }

    public function testDashboardPageDtoFromArrayWithNulls(): void
    {
        $data = [
            'name' => 'Page 1',
            'widgets' => [],
        ];

        $dto = DashboardPageDto::fromArray($data);

        self::assertSame('Page 1', $dto->getName());
        self::assertNull($dto->getDisplayPeriod());
        self::assertNull($dto->getSortorder());
        self::assertCount(0, $dto->getWidgets());
    }

    public function testDashboardWidgetDtoFromArray(): void
    {
        $data = [
            'type' => 'graph',
            'name' => 'CPU Graph',
            'x' => 0,
            'y' => 0,
            'width' => 12,
            'height' => 5,
            'fields' => [],
            'view_mode' => '0',
        ];

        $dto = DashboardWidgetDto::fromArray($data);

        self::assertSame('graph', $dto->getType());
        self::assertSame('CPU Graph', $dto->getName());
        self::assertSame(0, $dto->getX());
        self::assertSame(0, $dto->getY());
        self::assertSame(12, $dto->getWidth());
        self::assertSame(5, $dto->getHeight());
        self::assertEmpty($dto->getFields());
        self::assertSame('0', $dto->getViewMode());
    }

    public function testDashboardWidgetDtoFromArrayWithNulls(): void
    {
        $data = [
            'type' => 'graph',
            'name' => 'CPU Graph',
        ];

        $dto = DashboardWidgetDto::fromArray($data);

        self::assertSame('graph', $dto->getType());
        self::assertSame('CPU Graph', $dto->getName());
        self::assertNull($dto->getX());
        self::assertNull($dto->getY());
        self::assertNull($dto->getWidth());
        self::assertNull($dto->getHeight());
        self::assertEmpty($dto->getFields());
        self::assertNull($dto->getViewMode());
    }

    public function testGetDashboardResponseDtoFromArray(): void
    {
        $data = [
            [
                'dashboardid' => '12345',
                'name' => 'Test Dashboard 1',
            ],
            [
                'dashboardid' => '67890',
                'name' => 'Test Dashboard 2',
            ],
        ];

        $dto = GetDashboardResponseDto::fromArray($data);

        self::assertCount(2, $dto->getDashboards());
        self::assertSame('12345', $dto->getDashboards()[0]->getDashboardid());
        self::assertSame('67890', $dto->getDashboards()[1]->getDashboardid());
    }

    public function testGetDashboardResponseDtoFromEmptyArray(): void
    {
        $dto = GetDashboardResponseDto::fromArray([]);

        self::assertCount(0, $dto->getDashboards());
    }

    public function testGetDashboardDtoGetters(): void
    {
        $dto = new GetDashboardDto(
            dashboardids: ['12345'],
            filter: ['name' => 'Test'],
            output: 'extend',
            selectPages: true,
            selectUsers: true,
            selectUserGroups: true,
            sortfield: 1,
            sortorder: 'DESC',
            limit: 10,
            preservekeys: true,
        );

        self::assertSame(['12345'], $dto->getDashboardids());
        self::assertSame(['name' => 'Test'], $dto->getFilter());
        self::assertSame('extend', $dto->getOutput());
        self::assertTrue($dto->getSelectPages());
        self::assertTrue($dto->getSelectUsers());
        self::assertTrue($dto->getSelectUserGroups());
        self::assertSame(1, $dto->getSortfield());
        self::assertSame('DESC', $dto->getSortorder());
        self::assertSame(10, $dto->getLimit());
        self::assertTrue($dto->getPreservekeys());
    }

    public function testCreateDashboardDtoGetters(): void
    {
        $dashboards = [
            [
                'name' => 'Test Dashboard',
                'pages' => [],
            ],
        ];
        $dto = new CreateDashboardDto($dashboards);

        self::assertSame($dashboards, $dto->getDashboards());
    }

    public function testUpdateDashboardDtoGetters(): void
    {
        $dashboards = [
            [
                'dashboardid' => '12345',
                'name' => 'Updated Dashboard',
            ],
        ];
        $dto = new UpdateDashboardDto($dashboards);

        self::assertSame($dashboards, $dto->getDashboards());
    }

    public function testDeleteDashboardDtoGetters(): void
    {
        $dto = new DeleteDashboardDto(['12345', '67890']);

        self::assertSame(['12345', '67890'], $dto->getDashboardIds());
    }

    public function testCreateDashboardResponseDtoGetters(): void
    {
        $dto = new CreateDashboardResponseDto(['12345', '67890']);

        self::assertSame(['12345', '67890'], $dto->getDashboardids());
    }

    public function testUpdateDashboardResponseDtoGetters(): void
    {
        $dto = new UpdateDashboardResponseDto(['12345', '67890']);

        self::assertSame(['12345', '67890'], $dto->getDashboardids());
    }

    public function testDeleteDashboardResponseDto(): void
    {
        $dto = new DeleteDashboardResponseDto();

        self::assertInstanceOf(DeleteDashboardResponseDto::class, $dto);
    }

    public function testDashboardDtoGetters(): void
    {
        $pages = [
            new DashboardPageDto(
                name: 'Page 1',
                display_period: 1800,
                sortorder: 1,
                widgets: [],
            ),
        ];
        $dto = new DashboardDto(
            dashboardid: '12345',
            name: 'Test Dashboard',
            private: 0,
            userid: '1',
            display_period: 3600,
            auto_start: 1,
            pages: $pages,
        );

        self::assertSame('12345', $dto->getDashboardid());
        self::assertSame('Test Dashboard', $dto->getName());
        self::assertSame(0, $dto->getPrivate());
        self::assertSame('1', $dto->getUserid());
        self::assertSame(3600, $dto->getDisplayPeriod());
        self::assertSame(1, $dto->getAutoStart());
        self::assertSame($pages, $dto->getPages());
    }

    public function testDashboardPageDtoGetters(): void
    {
        $widgets = [
            new DashboardWidgetDto(
                type: 'graph',
                name: 'CPU Graph',
                x: 0,
                y: 0,
                width: 12,
                height: 5,
                fields: [],
                view_mode: '0',
            ),
        ];
        $dto = new DashboardPageDto(
            name: 'Page 1',
            display_period: 1800,
            sortorder: 1,
            widgets: $widgets,
        );

        self::assertSame('Page 1', $dto->getName());
        self::assertSame(1800, $dto->getDisplayPeriod());
        self::assertSame(1, $dto->getSortorder());
        self::assertSame($widgets, $dto->getWidgets());
    }

    public function testDashboardWidgetDtoGetters(): void
    {
        $dto = new DashboardWidgetDto(
            type: 'graph',
            name: 'CPU Graph',
            x: 0,
            y: 0,
            width: 12,
            height: 5,
            fields: [],
            view_mode: '0',
        );

        self::assertSame('graph', $dto->getType());
        self::assertSame('CPU Graph', $dto->getName());
        self::assertSame(0, $dto->getX());
        self::assertSame(0, $dto->getY());
        self::assertSame(12, $dto->getWidth());
        self::assertSame(5, $dto->getHeight());
        self::assertEmpty($dto->getFields());
        self::assertSame('0', $dto->getViewMode());
    }
}
