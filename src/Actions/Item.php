<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Actions;

use BytesCommerce\Zabbix\Actions\Dto\CreateItemDto;
use BytesCommerce\Zabbix\Actions\Dto\CreateSingleItemDto;
use BytesCommerce\Zabbix\Actions\Dto\DeleteItemDto;
use BytesCommerce\Zabbix\Actions\Dto\GetItemDto;
use BytesCommerce\Zabbix\Actions\Dto\GetItemResponseDto;
use BytesCommerce\Zabbix\Actions\Dto\UpdateItemDto;
use BytesCommerce\Zabbix\Actions\Dto\UpdateSingleItemDto;
use BytesCommerce\Zabbix\Enums\OutputEnum;
use BytesCommerce\Zabbix\Enums\ZabbixAction;

final class Item extends AbstractAction
{
    public static function getActionPrefix(): string
    {
        return 'item';
    }

    public function get(GetItemDto $dto): GetItemResponseDto
    {
        $params = array_filter((array) $dto, fn ($v) => $v !== null);
        if (!isset($params['output'])) {
            $params['output'] = OutputEnum::EXTEND->value;
        }

        $result = $this->client->call(ZabbixAction::ITEM_GET, $params);

        return GetItemResponseDto::fromArray($result);
    }

    public function create(CreateItemDto $dto): mixed
    {
        $items = array_map($this->mapCreateItem(...), $dto->items);

        return $this->client->call(ZabbixAction::ITEM_CREATE, $items);
    }

    public function update(UpdateItemDto $dto): mixed
    {
        $items = array_map($this->mapUpdateItem(...), $dto->items);

        return $this->client->call(ZabbixAction::ITEM_UPDATE, $items);
    }

    public function delete(DeleteItemDto $dto): mixed
    {
        return $this->client->call(ZabbixAction::ITEM_DELETE, $dto->itemIds);
    }

    private function mapCreateItem(CreateSingleItemDto $item): array
    {
        $data = [
            'name' => $item->name,
            'key_' => $item->key_,
            'hostid' => $item->hostid,
            'type' => $item->type->value,
            'value_type' => $item->value_type->value,
            'delay' => $item->delay,
        ];

        if ($item->interfaceid !== null) {
            $data['interfaceid'] = $item->interfaceid;
        }

        if ($item->preprocessing !== null) {
            $data['preprocessing'] = $item->preprocessing;
        }

        if ($item->tags !== null) {
            $data['tags'] = $item->tags;
        }

        if ($item->status !== null) {
            $data['status'] = $item->status->value;
        }

        return $data;
    }

    private function mapUpdateItem(UpdateSingleItemDto $item): array
    {
        $data = [
            'itemid' => $item->itemid,
        ];

        if ($item->name !== null) {
            $data['name'] = $item->name;
        }

        if ($item->key_ !== null) {
            $data['key_'] = $item->key_;
        }

        if ($item->hostid !== null) {
            $data['hostid'] = $item->hostid;
        }

        if ($item->type !== null) {
            $data['type'] = $item->type->value;
        }

        if ($item->value_type !== null) {
            $data['value_type'] = $item->value_type->value;
        }

        if ($item->delay !== null) {
            $data['delay'] = $item->delay;
        }

        if ($item->interfaceid !== null) {
            $data['interfaceid'] = $item->interfaceid;
        }

        if ($item->preprocessing !== null) {
            $data['preprocessing'] = $item->preprocessing;
        }

        if ($item->tags !== null) {
            $data['tags'] = $item->tags;
        }

        if ($item->status !== null) {
            $data['status'] = $item->status->value;
        }

        return $data;
    }
}
