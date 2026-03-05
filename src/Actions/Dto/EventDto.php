<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

use Webmozart\Assert\Assert;

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
        Assert::string($data['eventid'] ?? null);
        Assert::integerish($data['source'] ?? null);
        Assert::integerish($data['object'] ?? null);
        Assert::integerish($data['objectid'] ?? null);
        Assert::integerish($data['clock'] ?? null);
        Assert::integerish($data['value'] ?? null);
        Assert::integerish($data['acknowledged'] ?? null);
        Assert::integerish($data['ns'] ?? null);

        $severity = null;
        if (isset($data['severity'])) {
            Assert::integerish($data['severity']);
            $severity = (int) $data['severity'];
        }

        return new self(
            eventid: $data['eventid'],
            source: (int) $data['source'],
            object: (int) $data['object'],
            objectid: (int) $data['objectid'],
            clock: (int) $data['clock'],
            value: (int) $data['value'],
            acknowledged: (int) $data['acknowledged'],
            ns: (int) $data['ns'],
            name: isset($data['name']) && is_string($data['name']) ? $data['name'] : null,
            severity: $severity,
            acknowledges: isset($data['acknowledges']) && is_array($data['acknowledges']) ? $data['acknowledges'] : null,
            hosts: isset($data['hosts']) && is_array($data['hosts']) ? $data['hosts'] : null,
            tags: isset($data['tags']) && is_array($data['tags']) ? $data['tags'] : null,
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
