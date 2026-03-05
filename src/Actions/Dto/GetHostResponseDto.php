<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class GetHostResponseDto
{
    /**
     * @param list<HostDto> $hosts
     */
    public function __construct(
        public array $hosts,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $hosts = [];
        foreach ($data as $item) {
            if (is_array($item)) {
                $hosts[] = HostDto::fromArray($item);
            }
        }

        return new self(hosts: $hosts);
    }
}
