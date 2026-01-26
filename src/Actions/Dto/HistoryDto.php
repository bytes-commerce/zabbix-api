<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

use BytesCommerce\ZabbixApi\Enums\HistoryTypeEnum;

final readonly class HistoryDto
{
    public function __construct(
        public string $itemid,
        public int $clock,
        public string $value,
        public ?int $ns,
        public ?int $timestamp,
        public ?int $logeventid,
        public ?int $severity,
        public ?string $source,
        public ?string $eventid,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            itemid: $data['itemid'],
            clock: (int) $data['clock'],
            value: $data['value'],
            ns: isset($data['ns']) ? (int) $data['ns'] : null,
            timestamp: isset($data['timestamp']) ? (int) $data['timestamp'] : null,
            logeventid: isset($data['logeventid']) ? (int) $data['logeventid'] : null,
            severity: isset($data['severity']) ? (int) $data['severity'] : null,
            source: $data['source'] ?? null,
            eventid: $data['eventid'] ?? null,
        );
    }

    public function getItemid(): string
    {
        return $this->itemid;
    }

    public function getClock(): int
    {
        return $this->clock;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getNs(): ?int
    {
        return $this->ns;
    }

    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    public function getLogeventid(): ?int
    {
        return $this->logeventid;
    }

    public function getSeverity(): ?int
    {
        return $this->severity;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getEventid(): ?string
    {
        return $this->eventid;
    }
}
