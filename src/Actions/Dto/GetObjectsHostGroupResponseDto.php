<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class GetObjectsHostGroupResponseDto
{
    /**
     * @param list<HostGroupDto> $hostGroups
     */
    public function __construct(
        public array $hostGroups,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            hostGroups: array_map(HostGroupDto::fromArray(...), $data),
        );
    }

    /**
     * @return list<HostGroupDto>
     */
    public function getHostGroups(): array
    {
        return $this->hostGroups;
    }
}
