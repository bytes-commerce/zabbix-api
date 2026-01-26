<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions;

use BytesCommerce\ZabbixApi\Actions\Dto\CreateGraphDto;
use BytesCommerce\ZabbixApi\Actions\Dto\CreateGraphItemDto;
use BytesCommerce\ZabbixApi\Actions\Dto\CreateGraphResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\CreateSingleGraphDto;
use BytesCommerce\ZabbixApi\Actions\Dto\DeleteGraphDto;
use BytesCommerce\ZabbixApi\Actions\Dto\DeleteGraphResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\GetGraphDto;
use BytesCommerce\ZabbixApi\Actions\Dto\GetGraphResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\UpdateGraphDto;
use BytesCommerce\ZabbixApi\Actions\Dto\UpdateGraphItemDto;
use BytesCommerce\ZabbixApi\Actions\Dto\UpdateGraphResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\UpdateSingleGraphDto;
use BytesCommerce\ZabbixApi\Enums\OutputEnum;
use BytesCommerce\ZabbixApi\Enums\ZabbixAction;

final class Graph extends AbstractAction
{
    public static function getActionPrefix(): string
    {
        return 'graph';
    }

    public function get(GetGraphDto $dto): GetGraphResponseDto
    {
        $params = array_filter((array) $dto, fn ($v) => $v !== null);
        if (!isset($params['output'])) {
            $params['output'] = OutputEnum::EXTEND->value;
        }

        $result = $this->client->call(ZabbixAction::GRAPH_GET, $params);

        return GetGraphResponseDto::fromArray(is_array($result) ? $result : []);
    }

    public function create(CreateGraphDto $dto): CreateGraphResponseDto
    {
        $graphs = array_map($this->mapCreateGraph(...), $dto->graphs);

        $result = $this->client->call(ZabbixAction::GRAPH_CREATE, $graphs);

        return new CreateGraphResponseDto($result['graphids']);
    }

    public function update(UpdateGraphDto $dto): UpdateGraphResponseDto
    {
        $graphs = array_map($this->mapUpdateGraph(...), $dto->graphs);

        $result = $this->client->call(ZabbixAction::GRAPH_UPDATE, $graphs);

        return new UpdateGraphResponseDto($result['graphids']);
    }

    public function delete(DeleteGraphDto $dto): DeleteGraphResponseDto
    {
        $this->client->call(ZabbixAction::GRAPH_DELETE, $dto->graphIds);

        return new DeleteGraphResponseDto();
    }

    private function mapCreateGraph(CreateSingleGraphDto $graph): array
    {
        $data = [
            'name' => $graph->name,
            'gitems' => array_map($this->mapCreateGraphItem(...), $graph->gitems),
        ];

        if ($graph->width !== null) {
            $data['width'] = $graph->width;
        }
        if ($graph->height !== null) {
            $data['height'] = $graph->height;
        }
        if ($graph->yaxismin !== null) {
            $data['yaxismin'] = $graph->yaxismin;
        }
        if ($graph->yaxismax !== null) {
            $data['yaxismax'] = $graph->yaxismax;
        }
        if ($graph->show_work_period !== null) {
            $data['show_work_period'] = $graph->show_work_period;
        }
        if ($graph->show_triggers !== null) {
            $data['show_triggers'] = $graph->show_triggers;
        }
        if ($graph->graphtype !== null) {
            $data['graphtype'] = $graph->graphtype;
        }
        if ($graph->show_legend !== null) {
            $data['show_legend'] = $graph->show_legend;
        }
        if ($graph->show_3d !== null) {
            $data['show_3d'] = $graph->show_3d;
        }
        if ($graph->percent_left !== null) {
            $data['percent_left'] = $graph->percent_left;
        }
        if ($graph->percent_right !== null) {
            $data['percent_right'] = $graph->percent_right;
        }
        if ($graph->ymin_type !== null) {
            $data['ymin_type'] = $graph->ymin_type;
        }
        if ($graph->ymax_type !== null) {
            $data['ymax_type'] = $graph->ymax_type;
        }
        if ($graph->ymin_itemid !== null) {
            $data['ymin_itemid'] = $graph->ymin_itemid;
        }
        if ($graph->ymax_itemid !== null) {
            $data['ymax_itemid'] = $graph->ymax_itemid;
        }

        return $data;
    }

    private function mapCreateGraphItem(CreateGraphItemDto $item): array
    {
        $data = [
            'itemid' => $item->itemid,
        ];

        if ($item->drawtype !== null) {
            $data['drawtype'] = $item->drawtype;
        }
        if ($item->sortorder !== null) {
            $data['sortorder'] = $item->sortorder;
        }
        if ($item->color !== null) {
            $data['color'] = $item->color;
        }
        if ($item->yaxisside !== null) {
            $data['yaxisside'] = $item->yaxisside;
        }
        if ($item->calc_fnc !== null) {
            $data['calc_fnc'] = $item->calc_fnc;
        }
        if ($item->type !== null) {
            $data['type'] = $item->type;
        }

        return $data;
    }

    private function mapUpdateGraph(UpdateSingleGraphDto $graph): array
    {
        $data = [
            'graphid' => $graph->graphid,
        ];

        if ($graph->name !== null) {
            $data['name'] = $graph->name;
        }
        if ($graph->gitems !== null) {
            $data['gitems'] = array_map($this->mapUpdateGraphItem(...), $graph->gitems);
        }
        if ($graph->width !== null) {
            $data['width'] = $graph->width;
        }
        if ($graph->height !== null) {
            $data['height'] = $graph->height;
        }
        if ($graph->yaxismin !== null) {
            $data['yaxismin'] = $graph->yaxismin;
        }
        if ($graph->yaxismax !== null) {
            $data['yaxismax'] = $graph->yaxismax;
        }
        if ($graph->show_work_period !== null) {
            $data['show_work_period'] = $graph->show_work_period;
        }
        if ($graph->show_triggers !== null) {
            $data['show_triggers'] = $graph->show_triggers;
        }
        if ($graph->graphtype !== null) {
            $data['graphtype'] = $graph->graphtype;
        }
        if ($graph->show_legend !== null) {
            $data['show_legend'] = $graph->show_legend;
        }
        if ($graph->show_3d !== null) {
            $data['show_3d'] = $graph->show_3d;
        }
        if ($graph->percent_left !== null) {
            $data['percent_left'] = $graph->percent_left;
        }
        if ($graph->percent_right !== null) {
            $data['percent_right'] = $graph->percent_right;
        }
        if ($graph->ymin_type !== null) {
            $data['ymin_type'] = $graph->ymin_type;
        }
        if ($graph->ymax_type !== null) {
            $data['ymax_type'] = $graph->ymax_type;
        }
        if ($graph->ymin_itemid !== null) {
            $data['ymin_itemid'] = $graph->ymin_itemid;
        }
        if ($graph->ymax_itemid !== null) {
            $data['ymax_itemid'] = $graph->ymax_itemid;
        }

        return $data;
    }

    private function mapUpdateGraphItem(UpdateGraphItemDto $item): array
    {
        $data = [];

        if ($item->gitemid !== null) {
            $data['gitemid'] = $item->gitemid;
        }
        if ($item->itemid !== null) {
            $data['itemid'] = $item->itemid;
        }
        if ($item->drawtype !== null) {
            $data['drawtype'] = $item->drawtype;
        }
        if ($item->sortorder !== null) {
            $data['sortorder'] = $item->sortorder;
        }
        if ($item->color !== null) {
            $data['color'] = $item->color;
        }
        if ($item->yaxisside !== null) {
            $data['yaxisside'] = $item->yaxisside;
        }
        if ($item->calc_fnc !== null) {
            $data['calc_fnc'] = $item->calc_fnc;
        }
        if ($item->type !== null) {
            $data['type'] = $item->type;
        }

        return $data;
    }
}
