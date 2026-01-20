<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

use BytesCommerce\ZabbixApi\Enums\ItemTypeEnum;
use BytesCommerce\ZabbixApi\Enums\StatusEnum;
use BytesCommerce\ZabbixApi\Enums\ValueTypeEnum;
use Webmozart\Assert\Assert;

final readonly class UpdateSingleItemDto
{
    public function __construct(
        public string $itemid,
        public ?string $name = null,
        public ?string $key_ = null,
        public ?string $hostid = null,
        public ?ItemTypeEnum $type = null,
        public ?ValueTypeEnum $value_type = null,
        public ?string $delay = null,
        public ?string $interfaceid = null,
        public ?array $preprocessing = null,
        public ?array $tags = null,
        public ?StatusEnum $status = null,
    ) {
        Assert::stringNotEmpty($itemid);
        if ($name !== null) {
            Assert::stringNotEmpty($name);
        }
        if ($key_ !== null) {
            Assert::stringNotEmpty($key_);
        }
        if ($hostid !== null) {
            Assert::stringNotEmpty($hostid);
        }
        if ($delay !== null) {
            Assert::stringNotEmpty($delay);
        }
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
