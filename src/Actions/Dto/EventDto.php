<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class EventDto
{
    public function __construct(
        public string $eventid,
        public int $source,
        public int $object,
        public int $objectid,
        public int $clock,
        public int $value,
        public int $acknowledged,
        public int $ns,
        public ?string $name,
        public ?int $severity,
        public ?array $acknowledges,
        public ?array $hosts,
        public ?array $tags,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            eventid: $data['eventid'],
            source: $data['source'],
            object: $data['object'],
            objectid: $data['objectid'],
            clock: $data['clock'],
            value: $data['value'],
            acknowledged: $data['acknowledged'],
            ns: $data['ns'],
            name: $data['name'] ?? null,
            severity: $data['severity'] ?? null,
            acknowledges: $data['acknowledges'] ?? null,
            hosts: $data['hosts'] ?? null,
            tags: $data['tags'] ?? null,
        );
    }

    public function getEventid(): string
    {
        return $this->eventid;
    }

    public function getSource(): int
    {
        return $this->source;
    }

    public function getObject(): int
    {
        return $this->object;
    }

    public function getObjectid(): int
    {
        return $this->objectid;
    }

    public function getClock(): int
    {
        return $this->clock;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getAcknowledged(): int
    {
        return $this->acknowledged;
    }

    public function getNs(): int
    {
        return $this->ns;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSeverity(): ?int
    {
        return $this->severity;
    }

    public function getAcknowledges(): ?array
    {
        return $this->acknowledges;
    }

    public function getHosts(): ?array
    {
        return $this->hosts;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }
}
