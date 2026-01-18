<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Actions\Dto;

use BytesCommerce\Zabbix\Enums\EventSourceEnum;
use BytesCommerce\Zabbix\Enums\StatusEnum;

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
        return new self(
            actionid: $data['actionid'],
            name: $data['name'],
            eventsource: EventSourceEnum::from($data['eventsource']),
            esc_period: $data['esc_period'],
            status: isset($data['status']) ? StatusEnum::from($data['status']) : null,
            filter: $data['filter'] ?? null,
            operations: $data['operations'] ?? null,
            recovery_operations: $data['recovery_operations'] ?? null,
            update_operations: $data['update_operations'] ?? null,
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
