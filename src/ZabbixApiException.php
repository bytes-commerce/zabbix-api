<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix;

final class ZabbixApiException extends \RuntimeException
{
    public function __construct(
        string $message,
        private readonly int $errorCode,
        private readonly ?string $errorData = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $errorCode, $previous);
    }

    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    public function getErrorData(): ?string
    {
        return $this->errorData;
    }
}
