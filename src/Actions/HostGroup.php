<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions;

use BytesCommerce\ZabbixApi\Actions\Dto\CreateHostGroupDto;
use BytesCommerce\ZabbixApi\Actions\Dto\CreateHostGroupResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\DeleteHostGroupDto;
use BytesCommerce\ZabbixApi\Actions\Dto\DeleteHostGroupResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\ExistsHostGroupDto;
use BytesCommerce\ZabbixApi\Actions\Dto\ExistsHostGroupResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\GetHostGroupDto;
use BytesCommerce\ZabbixApi\Actions\Dto\GetHostGroupResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\GetObjectsHostGroupDto;
use BytesCommerce\ZabbixApi\Actions\Dto\GetObjectsHostGroupResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\IsReadableHostGroupDto;
use BytesCommerce\ZabbixApi\Actions\Dto\IsReadableHostGroupResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\IsWritableHostGroupDto;
use BytesCommerce\ZabbixApi\Actions\Dto\IsWritableHostGroupResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\MassAddHostGroupDto;
use BytesCommerce\ZabbixApi\Actions\Dto\MassAddHostGroupResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\MassRemoveHostGroupDto;
use BytesCommerce\ZabbixApi\Actions\Dto\MassRemoveHostGroupResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\MassUpdateHostGroupDto;
use BytesCommerce\ZabbixApi\Actions\Dto\MassUpdateHostGroupResponseDto;
use BytesCommerce\ZabbixApi\Actions\Dto\UpdateHostGroupDto;
use BytesCommerce\ZabbixApi\Actions\Dto\UpdateHostGroupResponseDto;
use BytesCommerce\ZabbixApi\Enums\OutputEnum;
use BytesCommerce\ZabbixApi\Enums\ZabbixAction;

final class HostGroup extends AbstractAction
{
    public static function getActionPrefix(): string
    {
        return 'hostgroup';
    }

    public function get(GetHostGroupDto $dto): GetHostGroupResponseDto
    {
        $params = array_filter((array) $dto, fn ($v) => $v !== null);
        if (!isset($params['output'])) {
            $params['output'] = OutputEnum::EXTEND->value;
        }

        $result = $this->client->call(ZabbixAction::HOSTGROUP_GET, $params);

        return GetHostGroupResponseDto::fromArray(is_array($result) ? $result : []);
    }

    public function create(CreateHostGroupDto $dto): CreateHostGroupResponseDto
    {
        $params = array_map($this->mapCreateHostGroup(...), $dto->hostGroups);

        $result = $this->client->call(ZabbixAction::HOSTGROUP_CREATE, $params);

        return new CreateHostGroupResponseDto($result['groupids']);
    }

    public function update(UpdateHostGroupDto $dto): UpdateHostGroupResponseDto
    {
        $params = array_map($this->mapUpdateHostGroup(...), $dto->hostGroups);

        $result = $this->client->call(ZabbixAction::HOSTGROUP_UPDATE, $params);

        return new UpdateHostGroupResponseDto($result['groupids']);
    }

    public function delete(DeleteHostGroupDto $dto): DeleteHostGroupResponseDto
    {
        $this->client->call(ZabbixAction::HOSTGROUP_DELETE, $dto->groupIds);

        return new DeleteHostGroupResponseDto();
    }

    public function exists(ExistsHostGroupDto $dto): ExistsHostGroupResponseDto
    {
        $params = array_filter((array) $dto, fn ($v) => $v !== null);

        $result = $this->client->call(ZabbixAction::HOSTGROUP_EXISTS, $params);

        return new ExistsHostGroupResponseDto((bool) $result);
    }

    public function getObjects(GetObjectsHostGroupDto $dto): GetObjectsHostGroupResponseDto
    {
        $params = array_filter((array) $dto, fn ($v) => $v !== null);

        $result = $this->client->call(ZabbixAction::HOSTGROUP_GETOBJECTS, $params);

        return GetObjectsHostGroupResponseDto::fromArray(is_array($result) ? $result : []);
    }

    public function isReadable(IsReadableHostGroupDto $dto): IsReadableHostGroupResponseDto
    {
        $params = array_filter((array) $dto, fn ($v) => $v !== null);

        $result = $this->client->call(ZabbixAction::HOSTGROUP_ISREADABLE, $params);

        return new IsReadableHostGroupResponseDto((bool) $result);
    }

    public function isWritable(IsWritableHostGroupDto $dto): IsWritableHostGroupResponseDto
    {
        $params = array_filter((array) $dto, fn ($v) => $v !== null);

        $result = $this->client->call(ZabbixAction::HOSTGROUP_ISWRITABLE, $params);

        return new IsWritableHostGroupResponseDto((bool) $result);
    }

    public function massAdd(MassAddHostGroupDto $dto): MassAddHostGroupResponseDto
    {
        $params = array_filter((array) $dto, fn ($v) => $v !== null);

        $result = $this->client->call(ZabbixAction::HOSTGROUP_MASSADD, $params);

        return new MassAddHostGroupResponseDto($result['groupids'] ?? []);
    }

    public function massRemove(MassRemoveHostGroupDto $dto): MassRemoveHostGroupResponseDto
    {
        $params = array_filter((array) $dto, fn ($v) => $v !== null);

        $result = $this->client->call(ZabbixAction::HOSTGROUP_MASSREMOVE, $params);

        return new MassRemoveHostGroupResponseDto($result['groupids'] ?? []);
    }

    public function massUpdate(MassUpdateHostGroupDto $dto): MassUpdateHostGroupResponseDto
    {
        $params = array_filter((array) $dto, fn ($v) => $v !== null);

        $result = $this->client->call(ZabbixAction::HOSTGROUP_MASSUPDATE, $params);

        return new MassUpdateHostGroupResponseDto($result['groupids'] ?? []);
    }

    private function mapCreateHostGroup(array $hostGroup): array
    {
        $data = [
            'name' => $hostGroup['name'],
        ];

        return $data;
    }

    private function mapUpdateHostGroup(array $hostGroup): array
    {
        $data = [
            'groupid' => $hostGroup['groupid'],
        ];

        if (isset($hostGroup['name'])) {
            $data['name'] = $hostGroup['name'];
        }

        return $data;
    }
}
