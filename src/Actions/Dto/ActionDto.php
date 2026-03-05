<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

use BytesCommerce\ZabbixApi\Enums\EventSourceEnum;
use BytesCommerce\ZabbixApi\Enums\StatusEnum;
use Webmozart\Assert\Assert;

final readonly class ActionDto
{
    public function __construct(
        public string $actionid,
        public string $name,
        public EventSourceEnum $eventsource,
        public string $esc_period,
        public ?StatusEnum $status,
        public ?array $filter,
        public ?array $operations,
        public ?array $recovery_operations,
        public ?array $update_operations,
    ) {
    }

    public static function fromArray(array $data): self
    {
        Assert::string($data['actionid'] ?? null);
        Assert::string($data['name'] ?? null);
        Assert::integerish($data['eventsource'] ?? null);
        Assert::string($data['esc_period'] ?? null);

        $status = null;
        if (isset($data['status'])) {
            Assert::integerish($data['status']);
            $status = StatusEnum::from((int) $data['status']);
        }

        return new self(
            actionid: $data['actionid'],
            name: $data['name'],
            eventsource: EventSourceEnum::from((int) $data['eventsource']),
            esc_period: $data['esc_period'],
            status: $status,
            filter: isset($data['filter']) && is_array($data['filter']) ? $data['filter'] : null,
            operations: isset($data['operations']) && is_array($data['operations']) ? $data['operations'] : null,
            recovery_operations: isset($data['recovery_operations']) && is_array($data['recovery_operations']) ? $data['recovery_operations'] : null,
            update_operations: isset($data['update_operations']) && is_array($data['update_operations']) ? $data['update_operations'] : null,
        );
    }

    public function getActionid(): string
    {
        return $this->actionid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEventsource(): EventSourceEnum
    {
        return $this->eventsource;
    }

    public function getEscPeriod(): string
    {
        return $this->esc_period;
    }

    public function getStatus(): ?StatusEnum
    {
        return $this->status;
    }

    public function getFilter(): ?array
    {
        return $this->filter;
    }

    public function getOperations(): ?array
    {
        return $this->operations;
    }

    public function getRecoveryOperations(): ?array
    {
        return $this->recovery_operations;
    }

    public function getUpdateOperations(): ?array
    {
        return $this->update_operations;
    }
}
