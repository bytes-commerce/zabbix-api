<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Actions\Dto;

use BytesCommerce\Zabbix\Enums\EventSourceEnum;
use BytesCommerce\Zabbix\Enums\StatusEnum;
use Webmozart\Assert\Assert;

final readonly class CreateSingleActionDto
{
    public function __construct(
        public string $name,
        public EventSourceEnum $eventsource,
        public string $esc_period,
        public array $operations,
        public ?StatusEnum $status = null,
        public ?FilterDto $filter = null,
        public ?array $recovery_operations = null,
        public ?array $update_operations = null,
    ) {
        Assert::stringNotEmpty($name);
        Assert::stringNotEmpty($esc_period);
        Assert::isArray($operations);
        Assert::allIsArray($operations);
        if ($recovery_operations !== null) {
            Assert::isArray($recovery_operations);
            Assert::allIsArray($recovery_operations);
        }
        if ($update_operations !== null) {
            Assert::isArray($update_operations);
            Assert::allIsArray($update_operations);
        }
    }
}
