<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

use Webmozart\Assert\Assert;

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
        Assert::string($data['alertid'] ?? null);
        Assert::string($data['actionid'] ?? null);
        Assert::string($data['eventid'] ?? null);
        Assert::string($data['userid'] ?? null);
        Assert::integerish($data['clock'] ?? null);
        Assert::integerish($data['mediatypeid'] ?? null);
        Assert::string($data['sendto'] ?? null);
        Assert::string($data['subject'] ?? null);
        Assert::string($data['message'] ?? null);
        Assert::integerish($data['status'] ?? null);
        Assert::integerish($data['retries'] ?? null);
        Assert::string($data['error'] ?? null);
        Assert::integerish($data['esc_step'] ?? null);

        return new self(
            alertid: $data['alertid'],
            actionid: $data['actionid'],
            eventid: $data['eventid'],
            userid: $data['userid'],
            clock: (int) $data['clock'],
            mediatypeid: (int) $data['mediatypeid'],
            sendto: $data['sendto'],
            subject: $data['subject'],
            message: $data['message'],
            status: (int) $data['status'],
            retries: (int) $data['retries'],
            error: $data['error'],
            esc_step: (int) $data['esc_step'],
            alerttype: isset($data['alerttype']) && is_string($data['alerttype']) ? $data['alerttype'] : null,
            p_eventid: isset($data['p_eventid']) && is_string($data['p_eventid']) ? $data['p_eventid'] : null,
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
