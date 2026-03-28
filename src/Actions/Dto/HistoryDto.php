<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

use Webmozart\Assert\Assert;

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
        Assert::string($data['itemid'] ?? null);
        Assert::integerish($data['clock'] ?? null);
        Assert::string($data['value'] ?? null);

        $ns = null;
        if (isset($data['ns'])) {
            Assert::integerish($data['ns']);
            $ns = (int) $data['ns'];
        }

        $timestamp = null;
        if (isset($data['timestamp'])) {
            Assert::integerish($data['timestamp']);
            $timestamp = (int) $data['timestamp'];
        }

        $logeventid = null;
        if (isset($data['logeventid'])) {
            Assert::integerish($data['logeventid']);
            $logeventid = (int) $data['logeventid'];
        }

        $severity = null;
        if (isset($data['severity'])) {
            Assert::integerish($data['severity']);
            $severity = (int) $data['severity'];
        }

        return new self(
            itemid: $data['itemid'],
            clock: (int) $data['clock'],
            value: $data['value'],
            ns: $ns,
            timestamp: $timestamp,
            logeventid: $logeventid,
            severity: $severity,
            source: isset($data['source']) && is_string($data['source']) ? $data['source'] : null,
            eventid: isset($data['eventid']) && is_string($data['eventid']) ? $data['eventid'] : null,
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
