<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class AlertDto
{
    public function __construct(
        public string $alertid,
        public string $actionid,
        public string $eventid,
        public string $userid,
        public int $clock,
        public int $mediatypeid,
        public string $sendto,
        public string $subject,
        public string $message,
        public int $status,
        public int $retries,
        public string $error,
        public int $esc_step,
        public ?string $alerttype,
        public ?string $p_eventid,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            alertid: $data['alertid'],
            actionid: $data['actionid'],
            eventid: $data['eventid'],
            userid: $data['userid'],
            clock: $data['clock'],
            mediatypeid: $data['mediatypeid'],
            sendto: $data['sendto'],
            subject: $data['subject'],
            message: $data['message'],
            status: $data['status'],
            retries: $data['retries'],
            error: $data['error'],
            esc_step: $data['esc_step'],
            alerttype: $data['alerttype'] ?? null,
            p_eventid: $data['p_eventid'] ?? null,
        );
    }

    public function getAlertid(): string
    {
        return $this->alertid;
    }

    public function getActionid(): string
    {
        return $this->actionid;
    }

    public function getEventid(): string
    {
        return $this->eventid;
    }

    public function getUserid(): string
    {
        return $this->userid;
    }

    public function getClock(): int
    {
        return $this->clock;
    }

    public function getMediatypeid(): int
    {
        return $this->mediatypeid;
    }

    public function getSendto(): string
    {
        return $this->sendto;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getRetries(): int
    {
        return $this->retries;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getEscStep(): int
    {
        return $this->esc_step;
    }

    public function getAlerttype(): ?string
    {
        return $this->alerttype;
    }

    public function getPEventid(): ?string
    {
        return $this->p_eventid;
    }
}
