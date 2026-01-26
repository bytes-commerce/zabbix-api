<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Tests;

use BytesCommerce\ZabbixApi\Actions\Dto\CreateGraphDto;
use BytesCommerce\ZabbixApi\Actions\Dto\CreateGraphItemDto;
use BytesCommerce\ZabbixApi\Actions\Dto\CreateGraphResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\CreateSingleGraphDto;
use BytesCommerce\ZabbixApi\Actions\Dto\DeleteGraphDto;
use BytesCommerce\ZabbixApi\Actions\Dto\DeleteGraphResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\GetGraphDto;
use BytesCommerce\ZabbixApi\Actions\Dto\GetGraphResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\GraphDto;
use BytesCommerce\ZabbixApi\Actions\Dto\GraphItemDto;
use BytesCommerce\ZabbixApi\Actions\Dto\UpdateGraphDto;
use BytesCommerce\ZabbixApi\Actions\Dto\UpdateGraphItemDto;
use BytesCommerce\ZabbixApi\Actions\Dto\UpdateGraphResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\UpdateSingleGraphDto;
use BytesCommerce\ZabbixApi\Actions\Graph;
use BytesCommerce\ZabbixApi\Enums\ZabbixAction;
use BytesCommerce\ZabbixApi\ZabbixClientInterface;
use PHPUnit\Framework\TestCase;

final class GraphTest extends TestCase
{
    private ZabbixClientInterface $zabbixClient;

    private Graph $graph;

    protected function setUp(): void
    {
        $this->zabbixClient = $this->createMock(ZabbixClientInterface::class);
        $this->graph = new Graph($this->zabbixClient);
    }

    public function testGetWithDefaultParameters(): void
    {
        $dto = new GetGraphDto(
            graphids: ['12345'],
            itemids: null,
            hostids: null,
            filter: null,
            output: null,
            selectGraphItems: null,
            selectHosts: null,
            selectItems: null,
            selectGraphDiscovery: null,
            sortfield: null,
            sortorder: null,
            limit: null,
            preservekeys: null,
        );
        $expectedParams = [
            'graphids' => ['12345'],
            'output' => 'extend',
        ];
        $expectedResult = [
            [
                'graphid' => '12345',
                'name' => 'Test Graph',
                'width' => 900,
                'height' => 200,
                'gitems' => [],
            ],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::GRAPH_GET, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->graph->get($dto);

        self::assertInstanceOf(GetGraphResponseDto::class, $result);
        self::assertCount(1, $result->getGraphs());
    }

    public function testGetWithCustomParameters(): void
    {
        $dto = new GetGraphDto(
            graphids: ['12345', '67890'],
            itemids: ['11111'],
            hostids: ['22222'],
            filter: ['name' => 'Test Graph'],
            output: 'extend',
            selectGraphItems: true,
            selectHosts: true,
            selectItems: true,
            selectGraphDiscovery: true,
            sortfield: 1,
            sortorder: 'DESC',
            limit: 10,
            preservekeys: true,
        );
        $expectedParams = [
            'graphids' => ['12345', '67890'],
            'itemids' => ['11111'],
            'hostids' => ['22222'],
            'filter' => ['name' => 'Test Graph'],
            'output' => 'extend',
            'selectGraphItems' => true,
            'selectHosts' => true,
            'selectItems' => true,
            'selectGraphDiscovery' => true,
            'sortfield' => 1,
            'sortorder' => 'DESC',
            'limit' => 10,
            'preservekeys' => true,
        ];
        $expectedResult = [
            [
                'graphid' => '12345',
                'name' => 'Test Graph',
            ],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::GRAPH_GET, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->graph->get($dto);

        self::assertInstanceOf(GetGraphResponseDto::class, $result);
    }

    public function testCreateValid(): void
    {
        $gitems = [
            new CreateGraphItemDto(
                itemid: '11111',
                drawtype: null,
                sortorder: null,
                color: null,
                yaxisside: null,
                calc_fnc: null,
                type: null,
            ),
        ];
        $graphs = [
            new CreateSingleGraphDto(
                name: 'Test Graph',
                gitems: $gitems,
                width: 900,
                height: 200,
                yaxismin: 0.0,
                yaxismax: 100.0,
                show_work_period: 1,
                show_triggers: 1,
                graphtype: 0,
                show_legend: 1,
                show_3d: 0,
                percent_left: 0.0,
                percent_right: 0.0,
                ymin_type: 0,
                ymax_type: 0,
                ymin_itemid: null,
                ymax_itemid: null,
            ),
        ];
        $dto = new CreateGraphDto($graphs);
        $expectedParams = [
            [
                'name' => 'Test Graph',
                'gitems' => [
                    [
                        'itemid' => '11111',
                    ],
                ],
                'width' => 900,
                'height' => 200,
                'yaxismin' => 0.0,
                'yaxismax' => 100.0,
                'show_work_period' => 1,
                'show_triggers' => 1,
                'graphtype' => 0,
                'show_legend' => 1,
                'show_3d' => 0,
                'percent_left' => 0.0,
                'percent_right' => 0.0,
                'ymin_type' => 0,
                'ymax_type' => 0,
            ],
        ];
        $expectedResult = [
            'graphids' => ['12345'],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::GRAPH_CREATE, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->graph->create($dto);

        self::assertInstanceOf(CreateGraphResponseDto::class, $result);
        self::assertSame(['12345'], $result->getGraphids());
    }

    public function testUpdateValid(): void
    {
        $gitems = [
            new UpdateGraphItemDto(
                gitemid: '33333',
                itemid: '11111',
                drawtype: null,
                sortorder: null,
                color: null,
                yaxisside: null,
                calc_fnc: null,
                type: null,
            ),
        ];
        $graphs = [
            new UpdateSingleGraphDto(
                graphid: '12345',
                name: 'Updated Graph',
                gitems: $gitems,
                width: 1000,
                height: 250,
                yaxismin: null,
                yaxismax: null,
                show_work_period: null,
                show_triggers: null,
                graphtype: null,
                show_legend: null,
                show_3d: null,
                percent_left: null,
                percent_right: null,
                ymin_type: null,
                ymax_type: null,
                ymin_itemid: null,
                ymax_itemid: null,
            ),
        ];
        $dto = new UpdateGraphDto($graphs);
        $expectedParams = [
            [
                'graphid' => '12345',
                'name' => 'Updated Graph',
                'gitems' => [
                    [
                        'gitemid' => '33333',
                        'itemid' => '11111',
                    ],
                ],
                'width' => 1000,
                'height' => 250,
            ],
        ];
        $expectedResult = [
            'graphids' => ['12345'],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::GRAPH_UPDATE, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->graph->update($dto);

        self::assertInstanceOf(UpdateGraphResponseDto::class, $result);
        self::assertSame(['12345'], $result->getGraphids());
    }

    public function testDelete(): void
    {
        $dto = new DeleteGraphDto(['12345', '67890']);
        $expectedParams = ['12345', '67890'];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::GRAPH_DELETE, $expectedParams);

        $result = $this->graph->delete($dto);

        self::assertInstanceOf(DeleteGraphResponseDto::class, $result);
    }

    public function testGraphDtoFromArray(): void
    {
        $data = [
            'graphid' => '12345',
            'name' => 'Test Graph',
            'width' => 900,
            'height' => 200,
            'yaxismin' => 0.0,
            'yaxismax' => 100.0,
            'show_work_period' => 1,
            'show_triggers' => 1,
            'graphtype' => 0,
            'show_legend' => 1,
            'show_3d' => 0,
            'percent_left' => 0.0,
            'percent_right' => 0.0,
            'ymin_type' => 0,
            'ymax_type' => 0,
            'ymin_itemid' => '11111',
            'ymax_itemid' => '22222',
            'gitems' => [
                [
                    'gitemid' => '33333',
                    'itemid' => '44444',
                    'drawtype' => 0,
                    'sortorder' => 0,
                    'color' => '00CC00',
                    'yaxisside' => 0,
                    'calc_fnc' => 2,
                    'type' => 0,
                ],
            ],
        ];

        $dto = GraphDto::fromArray($data);

        self::assertSame('12345', $dto->getGraphid());
        self::assertSame('Test Graph', $dto->getName());
        self::assertSame(900, $dto->getWidth());
        self::assertSame(200, $dto->getHeight());
        self::assertSame(0.0, $dto->getYaxismin());
        self::assertSame(100.0, $dto->getYaxismax());
        self::assertSame(1, $dto->getShowWorkPeriod());
        self::assertSame(1, $dto->getShowTriggers());
        self::assertSame(0, $dto->getGraphtype());
        self::assertSame(1, $dto->getShowLegend());
        self::assertSame(0, $dto->getShow3d());
        self::assertSame(0.0, $dto->getPercentLeft());
        self::assertSame(0.0, $dto->getPercentRight());
        self::assertSame(0, $dto->getYminType());
        self::assertSame(0, $dto->getYmaxType());
        self::assertSame('11111', $dto->getYminItemid());
        self::assertSame('22222', $dto->getYmaxItemid());
        self::assertCount(1, $dto->getGitems());
    }

    public function testGraphItemDtoFromArray(): void
    {
        $data = [
            'gitemid' => '33333',
            'itemid' => '44444',
            'drawtype' => 0,
            'sortorder' => 0,
            'color' => '00CC00',
            'yaxisside' => 0,
            'calc_fnc' => 2,
            'type' => 0,
        ];

        $dto = GraphItemDto::fromArray($data);

        self::assertSame('33333', $dto->getGitemid());
        self::assertSame('44444', $dto->getItemid());
        self::assertSame(0, $dto->getDrawtype());
        self::assertSame(0, $dto->getSortorder());
        self::assertSame('00CC00', $dto->getColor());
        self::assertSame(0, $dto->getYaxisside());
        self::assertSame(2, $dto->getCalcFnc());
        self::assertSame(0, $dto->getType());
    }

    public function testGetGraphResponseDtoFromArray(): void
    {
        $data = [
            [
                'graphid' => '12345',
                'name' => 'Test Graph 1',
            ],
            [
                'graphid' => '67890',
                'name' => 'Test Graph 2',
            ],
        ];

        $dto = GetGraphResponseDto::fromArray($data);

        self::assertCount(2, $dto->getGraphs());
        self::assertSame('12345', $dto->getGraphs()[0]->getGraphid());
        self::assertSame('67890', $dto->getGraphs()[1]->getGraphid());
    }

    public function testGetGraphResponseDtoFromEmptyArray(): void
    {
        $dto = GetGraphResponseDto::fromArray([]);

        self::assertCount(0, $dto->getGraphs());
    }

    public function testCreateGraphItemDtoGetters(): void
    {
        $dto = new CreateGraphItemDto(
            itemid: '11111',
            drawtype: 0,
            sortorder: 0,
            color: '00CC00',
            yaxisside: 0,
            calc_fnc: 2,
            type: 0,
        );

        self::assertSame('11111', $dto->getItemid());
        self::assertSame(0, $dto->getDrawtype());
        self::assertSame(0, $dto->getSortorder());
        self::assertSame('00CC00', $dto->getColor());
        self::assertSame(0, $dto->getYaxisside());
        self::assertSame(2, $dto->getCalcFnc());
        self::assertSame(0, $dto->getType());
    }

    public function testUpdateGraphItemDtoGetters(): void
    {
        $dto = new UpdateGraphItemDto(
            gitemid: '33333',
            itemid: '11111',
            drawtype: 0,
            sortorder: 0,
            color: '00CC00',
            yaxisside: 0,
            calc_fnc: 2,
            type: 0,
        );

        self::assertSame('33333', $dto->getGitemid());
        self::assertSame('11111', $dto->getItemid());
        self::assertSame(0, $dto->getDrawtype());
        self::assertSame(0, $dto->getSortorder());
        self::assertSame('00CC00', $dto->getColor());
        self::assertSame(0, $dto->getYaxisside());
        self::assertSame(2, $dto->getCalcFnc());
        self::assertSame(0, $dto->getType());
    }

    public function testCreateSingleGraphDtoGetters(): void
    {
        $gitems = [
            new CreateGraphItemDto(
                itemid: '11111',
                drawtype: null,
                sortorder: null,
                color: null,
                yaxisside: null,
                calc_fnc: null,
                type: null,
            ),
        ];

        $dto = new CreateSingleGraphDto(
            name: 'Test Graph',
            gitems: $gitems,
            width: 900,
            height: 200,
            yaxismin: 0.0,
            yaxismax: 100.0,
            show_work_period: 1,
            show_triggers: 1,
            graphtype: 0,
            show_legend: 1,
            show_3d: 0,
            percent_left: 0.0,
            percent_right: 0.0,
            ymin_type: 0,
            ymax_type: 0,
            ymin_itemid: '11111',
            ymax_itemid: '22222',
        );

        self::assertSame('Test Graph', $dto->getName());
        self::assertSame($gitems, $dto->getGitems());
        self::assertSame(900, $dto->getWidth());
        self::assertSame(200, $dto->getHeight());
        self::assertSame(0.0, $dto->getYaxismin());
        self::assertSame(100.0, $dto->getYaxismax());
        self::assertSame(1, $dto->getShowWorkPeriod());
        self::assertSame(1, $dto->getShowTriggers());
        self::assertSame(0, $dto->getGraphtype());
        self::assertSame(1, $dto->getShowLegend());
        self::assertSame(0, $dto->getShow3d());
        self::assertSame(0.0, $dto->getPercentLeft());
        self::assertSame(0.0, $dto->getPercentRight());
        self::assertSame(0, $dto->getYminType());
        self::assertSame(0, $dto->getYmaxType());
        self::assertSame('11111', $dto->getYminItemid());
        self::assertSame('22222', $dto->getYmaxItemid());
    }

    public function testUpdateSingleGraphDtoGetters(): void
    {
        $gitems = [
            new UpdateGraphItemDto(
                gitemid: '33333',
                itemid: '11111',
                drawtype: null,
                sortorder: null,
                color: null,
                yaxisside: null,
                calc_fnc: null,
                type: null,
            ),
        ];

        $dto = new UpdateSingleGraphDto(
            graphid: '12345',
            name: 'Updated Graph',
            gitems: $gitems,
            width: 1000,
            height: 250,
            yaxismin: null,
            yaxismax: null,
            show_work_period: null,
            show_triggers: null,
            graphtype: null,
            show_legend: null,
            show_3d: null,
            percent_left: null,
            percent_right: null,
            ymin_type: null,
            ymax_type: null,
            ymin_itemid: null,
            ymax_itemid: null,
        );

        self::assertSame('12345', $dto->getGraphid());
        self::assertSame('Updated Graph', $dto->getName());
        self::assertSame($gitems, $dto->getGitems());
        self::assertSame(1000, $dto->getWidth());
        self::assertSame(250, $dto->getHeight());
        self::assertNull($dto->getYaxismin());
        self::assertNull($dto->getYaxismax());
        self::assertNull($dto->getShowWorkPeriod());
        self::assertNull($dto->getShowTriggers());
        self::assertNull($dto->getGraphtype());
        self::assertNull($dto->getShowLegend());
        self::assertNull($dto->getShow3d());
        self::assertNull($dto->getPercentLeft());
        self::assertNull($dto->getPercentRight());
        self::assertNull($dto->getYminType());
        self::assertNull($dto->getYmaxType());
        self::assertNull($dto->getYminItemid());
        self::assertNull($dto->getYmaxItemid());
    }

    public function testGetGraphDtoGetters(): void
    {
        $dto = new GetGraphDto(
            graphids: ['12345'],
            itemids: ['11111'],
            hostids: ['22222'],
            filter: ['name' => 'Test'],
            output: 'extend',
            selectGraphItems: true,
            selectHosts: true,
            selectItems: true,
            selectGraphDiscovery: true,
            sortfield: 1,
            sortorder: 'DESC',
            limit: 10,
            preservekeys: true,
        );

        self::assertSame(['12345'], $dto->getGraphids());
        self::assertSame(['11111'], $dto->getItemids());
        self::assertSame(['22222'], $dto->getHostids());
        self::assertSame(['name' => 'Test'], $dto->getFilter());
        self::assertSame('extend', $dto->getOutput());
        self::assertTrue($dto->getSelectGraphItems());
        self::assertTrue($dto->getSelectHosts());
        self::assertTrue($dto->getSelectItems());
        self::assertTrue($dto->getSelectGraphDiscovery());
        self::assertSame(1, $dto->getSortfield());
        self::assertSame('DESC', $dto->getSortorder());
        self::assertSame(10, $dto->getLimit());
        self::assertTrue($dto->getPreservekeys());
    }

    public function testDeleteGraphDtoGetters(): void
    {
        $dto = new DeleteGraphDto(['12345', '67890']);

        self::assertSame(['12345', '67890'], $dto->getGraphIds());
    }

    public function testCreateGraphResponseDtoGetters(): void
    {
        $dto = new CreateGraphResponseDto(['12345', '67890']);

        self::assertSame(['12345', '67890'], $dto->getGraphids());
    }

    public function testUpdateGraphResponseDtoGetters(): void
    {
        $dto = new UpdateGraphResponseDto(['12345', '67890']);

        self::assertSame(['12345', '67890'], $dto->getGraphids());
    }

    public function testDeleteGraphResponseDto(): void
    {
        $dto = new DeleteGraphResponseDto();

        self::assertInstanceOf(DeleteGraphResponseDto::class, $dto);
    }
}
