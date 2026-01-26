<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class MassRemoveHostGroupDto
{
    /**
     * @param list<string>|null $groupids
     * @param list<string>|null $hostids
     * @param list<string>|null $templateids
     */
    public function __construct(
        public ?array $groupids,
        public ?array $hostids,
        public ?array $templateids,
    ) {
    }

    /**
     * @return list<string>|null
     */
    public function getGroupids(): ?array
    {
        return $this->groupids;
    }

    /**
     * @return list<string>|null
     */
    public function getHostids(): ?array
    {
        return $this->hostids;
    }

    /**
     * @return list<string>|null
     */
    public function getTemplateids(): ?array
    {
        return $this->templateids;
    }
}
