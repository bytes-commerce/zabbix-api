<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class PushHistoryDto
{
    public function __construct(
        public string $itemid,
        public int $clock,
        public string $value,
        public ?int $ns = null,
        public ?int $timestamp = null,
        public ?int $logeventid = null,
        public ?int $severity = null,
        public ?string $source = null,
        public ?string $eventid = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'itemid' => $this->itemid,
            'clock' => $this->clock,
            'value' => $this->value,
        ];

        if ($this->ns !== null) {
            $data['ns'] = $this->ns;
        }

        if ($this->timestamp !== null) {
            $data['timestamp'] = $this->timestamp;
        }

        if ($this->logeventid !== null) {
            $data['logeventid'] = $this->logeventid;
        }

        if ($this->severity !== null) {
            $data['severity'] = $this->severity;
        }

        if ($this->source !== null) {
            $data['source'] = $this->source;
        }

        if ($this->eventid !== null) {
            $data['eventid'] = $this->eventid;
        }

        return $data;
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
