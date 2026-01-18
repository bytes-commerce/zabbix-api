<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Actions\Dto;

final readonly class GetHostResponseDto
{
    /**
     * @param HostDto[] $hosts
     */
    public function __construct(
        public array $hosts,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            hosts: array_map(HostDto::fromArray(...), $data),
        );
    }
}
