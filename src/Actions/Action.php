<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions;

use BytesCommerce\ZabbixApi\Actions\Dto\CreateActionDto;
use BytesCommerce\ZabbixApi\Actions\Dto\CreateActionResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\CreateSingleActionDto;
use BytesCommerce\ZabbixApi\Actions\Dto\DeleteActionDto;
use BytesCommerce\ZabbixApi\Actions\Dto\DeleteActionResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\GetActionResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\UpdateActionDto;
use BytesCommerce\ZabbixApi\Actions\Dto\UpdateActionResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\UpdateSingleActionDto;
use BytesCommerce\ZabbixApi\Actions\Dto\GetActionDto;
use BytesCommerce\ZabbixApi\Enums\OutputEnum;
use BytesCommerce\ZabbixApi\Enums\ZabbixAction;

final class Action extends AbstractAction
{
    public static function getActionPrefix(): string
    {
        return 'action';
    }

    public function get(GetActionDto $dto): GetActionResponseDto
    {
        $params = array_filter((array) $dto, fn ($v) => $v !== null);
        if (!isset($params['output'])) {
            $params['output'] = OutputEnum::EXTEND->value;
        }

        $result = $this->client->call(ZabbixAction::ACTION_GET, $params);

        return GetActionResponseDto::fromArray($result);
    }

    public function create(CreateActionDto $dto): CreateActionResponseDto
    {
        $actions = array_map($this->mapCreateAction(...), $dto->actions);

        $result = $this->client->call(ZabbixAction::ACTION_CREATE, $actions);

        return new CreateActionResponseDto($result['actionids']);
    }

    public function update(UpdateActionDto $dto): UpdateActionResponseDto
    {
        $actions = array_map($this->mapUpdateAction(...), $dto->actions);

        $result = $this->client->call(ZabbixAction::ACTION_UPDATE, $actions);

        return new UpdateActionResponseDto($result['actionids']);
    }

    public function delete(DeleteActionDto $dto): DeleteActionResponseDto
    {
        $this->client->call(ZabbixAction::ACTION_DELETE, $dto->actionIds);

        return new DeleteActionResponseDto();
    }

    private function mapCreateAction(CreateSingleActionDto $action): array
    {
        $data = [
            'name' => $action->name,
            'eventsource' => $action->eventsource->value,
            'esc_period' => $action->esc_period,
            'operations' => $action->operations,
        ];

        if ($action->status !== null) {
            $data['status'] = $action->status->value;
        }
        if ($action->filter !== null) {
            $data['filter'] = [
                'evaltype' => $action->filter->evaltype->value,
                'conditions' => array_map(
                    fn ($c) => [
                        'conditiontype' => $c->conditiontype->value,
                        'operator' => $c->operator->value,
                        'value' => $c->value,
                    ],
                    $action->filter->conditions,
                ),
            ];
        }
        if ($action->recovery_operations !== null) {
            $data['recovery_operations'] = $action->recovery_operations;
        }
        if ($action->update_operations !== null) {
            $data['update_operations'] = $action->update_operations;
        }

        return $data;
    }

    private function mapUpdateAction(UpdateSingleActionDto $action): array
    {
        $data = [
            'actionid' => $action->actionid,
        ];

        if ($action->name !== null) {
            $data['name'] = $action->name;
        }
        if ($action->eventsource !== null) {
            $data['eventsource'] = $action->eventsource->value;
        }
        if ($action->esc_period !== null) {
            $data['esc_period'] = $action->esc_period;
        }
        if ($action->operations !== null) {
            $data['operations'] = $action->operations;
        }
        if ($action->status !== null) {
            $data['status'] = $action->status->value;
        }
        if ($action->filter !== null) {
            $data['filter'] = [
                'evaltype' => $action->filter->evaltype->value,
                'conditions' => array_map(
                    fn ($c) => [
                        'conditiontype' => $c->conditiontype->value,
                        'operator' => $c->operator->value,
                        'value' => $c->value,
                    ],
                    $action->filter->conditions,
                ),
            ];
        }
        if ($action->recovery_operations !== null) {
            $data['recovery_operations'] = $action->recovery_operations;
        }
        if ($action->update_operations !== null) {
            $data['update_operations'] = $action->update_operations;
        }

        return $data;
    }
}
