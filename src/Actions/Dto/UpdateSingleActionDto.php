<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Actions\Dto;

use BytesCommerce\Zabbix\Enums\EventSourceEnum;
use BytesCommerce\Zabbix\Enums\StatusEnum;
use Webmozart\Assert\Assert;

final readonly class UpdateSingleActionDto
{
    public function __construct(
        public string $actionid,
        public ?string $name = null,
        public ?EventSourceEnum $eventsource = null,
        public ?string $esc_period = null,
        public ?array $operations = null,
        public ?StatusEnum $status = null,
        public ?FilterDto $filter = null,
        public ?array $recovery_operations = null,
        public ?array $update_operations = null,
    ) {
        Assert::stringNotEmpty($actionid);
        if ($name !== null) {
            Assert::stringNotEmpty($name);
        }
        if ($esc_period !== null) {
            Assert::stringNotEmpty($esc_period);
        }
        if ($operations !== null) {
            Assert::isArray($operations);
            Assert::allIsArray($operations);
        }
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
