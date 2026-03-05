<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class GetHostGroupResponseDto
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
        $hostGroups = [];
        foreach ($data as $item) {
            if (is_array($item)) {
                $hostGroups[] = HostGroupDto::fromArray($item);
            }
        }

        return new self(hostGroups: $hostGroups);
    }

    /**
     * @return list<HostGroupDto>
     */
    public function getHostGroups(): array
    {
        return $this->hostGroups;
    }
}
