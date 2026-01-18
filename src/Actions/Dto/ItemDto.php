<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Actions\Dto;

use BytesCommerce\Zabbix\Enums\StatusEnum;
use BytesCommerce\Zabbix\Zabbix\ItemTypeEnum;
use BytesCommerce\Zabbix\Zabbix\ValueTypeEnum;

final readonly class ItemDto
{
    public function __construct(
        public string $itemid,
        public string $name,
        public string $key_,
        public string $hostid,
        public ItemTypeEnum $type,
        public ValueTypeEnum $value_type,
        public string $delay,
        public ?string $interfaceid,
        public ?array $preprocessing,
        public ?array $tags,
        public ?StatusEnum $status,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            itemid: $data['itemid'],
            name: $data['name'],
            key_: $data['key_'],
            hostid: $data['hostid'],
            type: ItemTypeEnum::from($data['type']),
            value_type: ValueTypeEnum::from($data['value_type']),
            delay: $data['delay'],
            interfaceid: $data['interfaceid'] ?? null,
            preprocessing: $data['preprocessing'] ?? null,
            tags: $data['tags'] ?? null,
            status: isset($data['status']) ? StatusEnum::from($data['status']) : null,
        );
    }

    public function getItemid(): string
    {
        return $this->itemid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getKey(): string
    {
        return $this->key_;
    }

    public function getHostid(): string
    {
        return $this->hostid;
    }

    public function getType(): ItemTypeEnum
    {
        return $this->type;
    }

    public function getValueType(): ValueTypeEnum
    {
        return $this->value_type;
    }

    public function getDelay(): string
    {
        return $this->delay;
    }

    public function getInterfaceid(): ?string
    {
        return $this->interfaceid;
    }

    public function getPreprocessing(): ?array
    {
        return $this->preprocessing;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function getStatus(): ?StatusEnum
    {
        return $this->status;
    }
}
