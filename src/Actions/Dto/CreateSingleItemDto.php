<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Actions\Dto;

use BytesCommerce\Zabbix\Enums\ItemTypeEnum;
use BytesCommerce\Zabbix\Enums\StatusEnum;
use BytesCommerce\Zabbix\Enums\ValueTypeEnum;
use Webmozart\Assert\Assert;

final readonly class CreateSingleItemDto
{
    public function __construct(
        public string $name,
        public string $key_,
        public string $hostid,
        public ItemTypeEnum $type,
        public ValueTypeEnum $value_type,
        public string $delay,
        public ?string $interfaceid = null,
        public ?array $preprocessing = null,
        public ?array $tags = null,
        public ?StatusEnum $status = null,
    ) {
        Assert::stringNotEmpty($name);
        Assert::stringNotEmpty($key_);
        Assert::stringNotEmpty($hostid);
        Assert::stringNotEmpty($delay);
        if ($interfaceid !== null) {
            Assert::stringNotEmpty($interfaceid);
        }
        if ($preprocessing !== null) {
            Assert::isArray($preprocessing);
            Assert::allIsArray($preprocessing);
        }
        if ($tags !== null) {
            Assert::isArray($tags);
            Assert::allIsArray($tags);
        }
    }
}
