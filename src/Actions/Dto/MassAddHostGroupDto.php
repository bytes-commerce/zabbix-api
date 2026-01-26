<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class MassAddHostGroupDto
{
    /**
     * @param list<string>|null $groups
     * @param list<string>|null $hosts
     * @param list<string>|null $templates
     */
    public function __construct(
        public ?array $groups,
        public ?array $hosts,
        public ?array $templates,
    ) {
    }

    /**
     * @return list<string>|null
     */
    public function getGroups(): ?array
    {
        return $this->groups;
    }

    /**
     * @return list<string>|null
     */
    public function getHosts(): ?array
    {
        return $this->hosts;
    }

    /**
     * @return list<string>|null
     */
    public function getTemplates(): ?array
    {
        return $this->templates;
    }
}
