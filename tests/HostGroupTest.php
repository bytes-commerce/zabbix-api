<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Tests;

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
use BytesCommerce\ZabbixApi\Actions\Dto\HostGroupDto;
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
use BytesCommerce\ZabbixApi\Actions\HostGroup;
use BytesCommerce\ZabbixApi\Enums\ZabbixAction;
use BytesCommerce\ZabbixApi\ZabbixClientInterface;
use PHPUnit\Framework\TestCase;

final class HostGroupTest extends TestCase
{
    private ZabbixClientInterface $zabbixClient;

    private HostGroup $hostGroup;

    protected function setUp(): void
    {
        $this->zabbixClient = $this->createMock(ZabbixClientInterface::class);
        $this->hostGroup = new HostGroup($this->zabbixClient);
    }

    public function testGetWithDefaultParameters(): void
    {
        $dto = new GetHostGroupDto(
            groupids: ['12345'],
            hostids: null,
            filter: null,
            output: null,
            selectHosts: null,
            selectTemplates: null,
            sortfield: null,
            sortorder: null,
            limit: null,
            preservekeys: null,
        );
        $expectedParams = [
            'groupids' => ['12345'],
            'output' => 'extend',
        ];
        $expectedResult = [
            [
                'groupid' => '12345',
                'name' => 'Test Host Group',
                'flags' => 0,
                'internal' => 0,
            ],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HOSTGROUP_GET, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->hostGroup->get($dto);

        self::assertInstanceOf(GetHostGroupResponseDto::class, $result);
        self::assertCount(1, $result->getHostGroups());
    }

    public function testGetWithCustomParameters(): void
    {
        $dto = new GetHostGroupDto(
            groupids: ['12345', '67890'],
            hostids: ['11111'],
            filter: ['name' => 'Test Host Group'],
            output: 'extend',
            selectHosts: true,
            selectTemplates: true,
            sortfield: 1,
            sortorder: 'DESC',
            limit: 10,
            preservekeys: true,
        );
        $expectedParams = [
            'groupids' => ['12345', '67890'],
            'hostids' => ['11111'],
            'filter' => ['name' => 'Test Host Group'],
            'output' => 'extend',
            'selectHosts' => true,
            'selectTemplates' => true,
            'sortfield' => 1,
            'sortorder' => 'DESC',
            'limit' => 10,
            'preservekeys' => true,
        ];
        $expectedResult = [
            [
                'groupid' => '12345',
                'name' => 'Test Host Group',
            ],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HOSTGROUP_GET, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->hostGroup->get($dto);

        self::assertInstanceOf(GetHostGroupResponseDto::class, $result);
    }

    public function testCreateValid(): void
    {
        $hostGroups = [
            ['name' => 'Test Host Group'],
        ];
        $dto = new CreateHostGroupDto($hostGroups);
        $expectedParams = [
            [
                'name' => 'Test Host Group',
            ],
        ];
        $expectedResult = [
            'groupids' => ['12345'],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HOSTGROUP_CREATE, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->hostGroup->create($dto);

        self::assertInstanceOf(CreateHostGroupResponseDto::class, $result);
        self::assertSame(['12345'], $result->getGroupids());
    }

    public function testUpdateValid(): void
    {
        $hostGroups = [
            [
                'groupid' => '12345',
                'name' => 'Updated Host Group',
            ],
        ];
        $dto = new UpdateHostGroupDto($hostGroups);
        $expectedParams = [
            [
                'groupid' => '12345',
                'name' => 'Updated Host Group',
            ],
        ];
        $expectedResult = [
            'groupids' => ['12345'],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HOSTGROUP_UPDATE, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->hostGroup->update($dto);

        self::assertInstanceOf(UpdateHostGroupResponseDto::class, $result);
        self::assertSame(['12345'], $result->getGroupids());
    }

    public function testDelete(): void
    {
        $dto = new DeleteHostGroupDto(['12345', '67890']);
        $expectedParams = ['12345', '67890'];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HOSTGROUP_DELETE, $expectedParams);

        $result = $this->hostGroup->delete($dto);

        self::assertInstanceOf(DeleteHostGroupResponseDto::class, $result);
    }

    public function testExists(): void
    {
        $dto = new ExistsHostGroupDto(
            groupids: ['12345'],
            hostids: null,
            filter: null,
            name: null,
        );
        $expectedParams = [
            'groupids' => ['12345'],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HOSTGROUP_EXISTS, $expectedParams)
            ->willReturn(true);

        $result = $this->hostGroup->exists($dto);

        self::assertInstanceOf(ExistsHostGroupResponseDto::class, $result);
        self::assertTrue($result->getExists());
    }

    public function testGetObjects(): void
    {
        $dto = new GetObjectsHostGroupDto(
            groupids: ['12345'],
            hostids: null,
            filter: null,
            name: null,
        );
        $expectedParams = [
            'groupids' => ['12345'],
        ];
        $expectedResult = [
            [
                'groupid' => '12345',
                'name' => 'Test Host Group',
            ],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HOSTGROUP_GETOBJECTS, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->hostGroup->getObjects($dto);

        self::assertInstanceOf(GetObjectsHostGroupResponseDto::class, $result);
        self::assertCount(1, $result->getHostGroups());
    }

    public function testIsReadable(): void
    {
        $dto = new IsReadableHostGroupDto(
            groupids: ['12345'],
            hostids: null,
        );
        $expectedParams = [
            'groupids' => ['12345'],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HOSTGROUP_ISREADABLE, $expectedParams)
            ->willReturn(true);

        $result = $this->hostGroup->isReadable($dto);

        self::assertInstanceOf(IsReadableHostGroupResponseDto::class, $result);
        self::assertTrue($result->getIsReadable());
    }

    public function testIsWritable(): void
    {
        $dto = new IsWritableHostGroupDto(
            groupids: ['12345'],
            hostids: null,
        );
        $expectedParams = [
            'groupids' => ['12345'],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HOSTGROUP_ISWRITABLE, $expectedParams)
            ->willReturn(true);

        $result = $this->hostGroup->isWritable($dto);

        self::assertInstanceOf(IsWritableHostGroupResponseDto::class, $result);
        self::assertTrue($result->getIsWritable());
    }

    public function testMassAdd(): void
    {
        $dto = new MassAddHostGroupDto(
            groups: ['12345'],
            hosts: ['11111'],
            templates: null,
        );
        $expectedParams = [
            'groups' => ['12345'],
            'hosts' => ['11111'],
        ];
        $expectedResult = [
            'groupids' => ['12345'],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HOSTGROUP_MASSADD, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->hostGroup->massAdd($dto);

        self::assertInstanceOf(MassAddHostGroupResponseDto::class, $result);
        self::assertSame(['12345'], $result->getGroupids());
    }

    public function testMassRemove(): void
    {
        $dto = new MassRemoveHostGroupDto(
            groupids: ['12345'],
            hostids: ['11111'],
            templateids: null,
        );
        $expectedParams = [
            'groupids' => ['12345'],
            'hostids' => ['11111'],
        ];
        $expectedResult = [
            'groupids' => ['12345'],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HOSTGROUP_MASSREMOVE, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->hostGroup->massRemove($dto);

        self::assertInstanceOf(MassRemoveHostGroupResponseDto::class, $result);
        self::assertSame(['12345'], $result->getGroupids());
    }

    public function testMassUpdate(): void
    {
        $dto = new MassUpdateHostGroupDto(
            groups: ['12345'],
            hosts: ['11111'],
            templates: null,
        );
        $expectedParams = [
            'groups' => ['12345'],
            'hosts' => ['11111'],
        ];
        $expectedResult = [
            'groupids' => ['12345'],
        ];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with(ZabbixAction::HOSTGROUP_MASSUPDATE, $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->hostGroup->massUpdate($dto);

        self::assertInstanceOf(MassUpdateHostGroupResponseDto::class, $result);
        self::assertSame(['12345'], $result->getGroupids());
    }

    public function testHostGroupDtoFromArray(): void
    {
        $data = [
            'groupid' => '12345',
            'name' => 'Test Host Group',
            'flags' => 0,
            'internal' => 0,
        ];

        $dto = HostGroupDto::fromArray($data);

        self::assertSame('12345', $dto->getGroupid());
        self::assertSame('Test Host Group', $dto->getName());
        self::assertSame(0, $dto->getFlags());
        self::assertSame(0, $dto->getInternal());
    }

    public function testHostGroupDtoFromArrayWithNulls(): void
    {
        $data = [
            'groupid' => '12345',
            'name' => 'Test Host Group',
        ];

        $dto = HostGroupDto::fromArray($data);

        self::assertSame('12345', $dto->getGroupid());
        self::assertSame('Test Host Group', $dto->getName());
        self::assertNull($dto->getFlags());
        self::assertNull($dto->getInternal());
    }

    public function testGetHostGroupResponseDtoFromArray(): void
    {
        $data = [
            [
                'groupid' => '12345',
                'name' => 'Test Host Group 1',
            ],
            [
                'groupid' => '67890',
                'name' => 'Test Host Group 2',
            ],
        ];

        $dto = GetHostGroupResponseDto::fromArray($data);

        self::assertCount(2, $dto->getHostGroups());
        self::assertSame('12345', $dto->getHostGroups()[0]->getGroupid());
        self::assertSame('67890', $dto->getHostGroups()[1]->getGroupid());
    }

    public function testGetHostGroupResponseDtoFromEmptyArray(): void
    {
        $dto = GetHostGroupResponseDto::fromArray([]);

        self::assertCount(0, $dto->getHostGroups());
    }

    public function testGetObjectsHostGroupResponseDtoFromArray(): void
    {
        $data = [
            [
                'groupid' => '12345',
                'name' => 'Test Host Group 1',
            ],
            [
                'groupid' => '67890',
                'name' => 'Test Host Group 2',
            ],
        ];

        $dto = GetObjectsHostGroupResponseDto::fromArray($data);

        self::assertCount(2, $dto->getHostGroups());
        self::assertSame('12345', $dto->getHostGroups()[0]->getGroupid());
        self::assertSame('67890', $dto->getHostGroups()[1]->getGroupid());
    }

    public function testGetHostGroupDtoGetters(): void
    {
        $dto = new GetHostGroupDto(
            groupids: ['12345'],
            hostids: ['11111'],
            filter: ['name' => 'Test'],
            output: 'extend',
            selectHosts: true,
            selectTemplates: true,
            sortfield: 1,
            sortorder: 'DESC',
            limit: 10,
            preservekeys: true,
        );

        self::assertSame(['12345'], $dto->getGroupids());
        self::assertSame(['11111'], $dto->getHostids());
        self::assertSame(['name' => 'Test'], $dto->getFilter());
        self::assertSame('extend', $dto->getOutput());
        self::assertTrue($dto->getSelectHosts());
        self::assertTrue($dto->getSelectTemplates());
        self::assertSame(1, $dto->getSortfield());
        self::assertSame('DESC', $dto->getSortorder());
        self::assertSame(10, $dto->getLimit());
        self::assertTrue($dto->getPreservekeys());
    }

    public function testCreateHostGroupDtoGetters(): void
    {
        $hostGroups = [
            ['name' => 'Test Host Group'],
        ];
        $dto = new CreateHostGroupDto($hostGroups);

        self::assertSame($hostGroups, $dto->getHostGroups());
    }

    public function testUpdateHostGroupDtoGetters(): void
    {
        $hostGroups = [
            [
                'groupid' => '12345',
                'name' => 'Updated Host Group',
            ],
        ];
        $dto = new UpdateHostGroupDto($hostGroups);

        self::assertSame($hostGroups, $dto->getHostGroups());
    }

    public function testDeleteHostGroupDtoGetters(): void
    {
        $dto = new DeleteHostGroupDto(['12345', '67890']);

        self::assertSame(['12345', '67890'], $dto->getGroupIds());
    }

    public function testExistsHostGroupDtoGetters(): void
    {
        $dto = new ExistsHostGroupDto(
            groupids: ['12345'],
            hostids: ['11111'],
            filter: ['name' => 'Test'],
            name: 'Test Host Group',
        );

        self::assertSame(['12345'], $dto->getGroupids());
        self::assertSame(['11111'], $dto->getHostids());
        self::assertSame(['name' => 'Test'], $dto->getFilter());
        self::assertSame('Test Host Group', $dto->getName());
    }

    public function testGetObjectsHostGroupDtoGetters(): void
    {
        $dto = new GetObjectsHostGroupDto(
            groupids: ['12345'],
            hostids: ['11111'],
            filter: ['name' => 'Test'],
            name: 'Test Host Group',
        );

        self::assertSame(['12345'], $dto->getGroupids());
        self::assertSame(['11111'], $dto->getHostids());
        self::assertSame(['name' => 'Test'], $dto->getFilter());
        self::assertSame('Test Host Group', $dto->getName());
    }

    public function testIsReadableHostGroupDtoGetters(): void
    {
        $dto = new IsReadableHostGroupDto(
            groupids: ['12345'],
            hostids: ['11111'],
        );

        self::assertSame(['12345'], $dto->getGroupids());
        self::assertSame(['11111'], $dto->getHostids());
    }

    public function testIsWritableHostGroupDtoGetters(): void
    {
        $dto = new IsWritableHostGroupDto(
            groupids: ['12345'],
            hostids: ['11111'],
        );

        self::assertSame(['12345'], $dto->getGroupids());
        self::assertSame(['11111'], $dto->getHostids());
    }

    public function testMassAddHostGroupDtoGetters(): void
    {
        $dto = new MassAddHostGroupDto(
            groups: ['12345'],
            hosts: ['11111'],
            templates: ['22222'],
        );

        self::assertSame(['12345'], $dto->getGroups());
        self::assertSame(['11111'], $dto->getHosts());
        self::assertSame(['22222'], $dto->getTemplates());
    }

    public function testMassRemoveHostGroupDtoGetters(): void
    {
        $dto = new MassRemoveHostGroupDto(
            groupids: ['12345'],
            hostids: ['11111'],
            templateids: ['22222'],
        );

        self::assertSame(['12345'], $dto->getGroupids());
        self::assertSame(['11111'], $dto->getHostids());
        self::assertSame(['22222'], $dto->getTemplateids());
    }

    public function testMassUpdateHostGroupDtoGetters(): void
    {
        $dto = new MassUpdateHostGroupDto(
            groups: ['12345'],
            hosts: ['11111'],
            templates: ['22222'],
        );

        self::assertSame(['12345'], $dto->getGroups());
        self::assertSame(['11111'], $dto->getHosts());
        self::assertSame(['22222'], $dto->getTemplates());
    }

    public function testCreateHostGroupResponseDtoGetters(): void
    {
        $dto = new CreateHostGroupResponseDto(['12345', '67890']);

        self::assertSame(['12345', '67890'], $dto->getGroupids());
    }

    public function testUpdateHostGroupResponseDtoGetters(): void
    {
        $dto = new UpdateHostGroupResponseDto(['12345', '67890']);

        self::assertSame(['12345', '67890'], $dto->getGroupids());
    }

    public function testDeleteHostGroupResponseDto(): void
    {
        $dto = new DeleteHostGroupResponseDto();

        self::assertInstanceOf(DeleteHostGroupResponseDto::class, $dto);
    }

    public function testExistsHostGroupResponseDtoGetters(): void
    {
        $dto = new ExistsHostGroupResponseDto(true);

        self::assertTrue($dto->getExists());
    }

    public function testIsReadableHostGroupResponseDtoGetters(): void
    {
        $dto = new IsReadableHostGroupResponseDto(true);

        self::assertTrue($dto->getIsReadable());
    }

    public function testIsWritableHostGroupResponseDtoGetters(): void
    {
        $dto = new IsWritableHostGroupResponseDto(true);

        self::assertTrue($dto->getIsWritable());
    }

    public function testMassAddHostGroupResponseDtoGetters(): void
    {
        $dto = new MassAddHostGroupResponseDto(['12345', '67890']);

        self::assertSame(['12345', '67890'], $dto->getGroupids());
    }

    public function testMassRemoveHostGroupResponseDtoGetters(): void
    {
        $dto = new MassRemoveHostGroupResponseDto(['12345', '67890']);

        self::assertSame(['12345', '67890'], $dto->getGroupids());
    }

    public function testMassUpdateHostGroupResponseDtoGetters(): void
    {
        $dto = new MassUpdateHostGroupResponseDto(['12345', '67890']);

        self::assertSame(['12345', '67890'], $dto->getGroupids());
    }

    public function testHostGroupDtoGetters(): void
    {
        $dto = new HostGroupDto(
            groupid: '12345',
            name: 'Test Host Group',
            flags: 0,
            internal: 0,
        );

        self::assertSame('12345', $dto->getGroupid());
        self::assertSame('Test Host Group', $dto->getName());
        self::assertSame(0, $dto->getFlags());
        self::assertSame(0, $dto->getInternal());
    }
}
