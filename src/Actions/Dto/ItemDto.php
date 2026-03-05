<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

use BytesCommerce\ZabbixApi\Enums\ItemTypeEnum;
use BytesCommerce\ZabbixApi\Enums\StatusEnum;
use BytesCommerce\ZabbixApi\Enums\ValueTypeEnum;
use Webmozart\Assert\Assert;

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
        Assert::string($data['itemid'] ?? null);
        Assert::string($data['name'] ?? null);
        Assert::string($data['key_'] ?? null);
        Assert::string($data['hostid'] ?? null);
        Assert::integerish($data['type'] ?? null);
        Assert::integerish($data['value_type'] ?? null);
        Assert::string($data['delay'] ?? null);

        $status = null;
        if (isset($data['status'])) {
            Assert::integerish($data['status']);
            $status = StatusEnum::from((int) $data['status']);
        }

        return new self(
            itemid: $data['itemid'],
            name: $data['name'],
            key_: $data['key_'],
            hostid: $data['hostid'],
            type: ItemTypeEnum::from((int) $data['type']),
            value_type: ValueTypeEnum::from((int) $data['value_type']),
            delay: $data['delay'],
            interfaceid: isset($data['interfaceid']) && is_string($data['interfaceid']) ? $data['interfaceid'] : null,
            preprocessing: isset($data['preprocessing']) && is_array($data['preprocessing']) ? $data['preprocessing'] : null,
            tags: isset($data['tags']) && is_array($data['tags']) ? $data['tags'] : null,
            status: $status,
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
