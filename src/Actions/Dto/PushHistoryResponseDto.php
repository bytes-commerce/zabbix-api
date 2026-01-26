<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class PushHistoryResponseDto
{
    /**
     * @param list<string> $historyids
     */
    public function __construct(
        public array $historyids,
        public string $response,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $historyids = isset($data['historyids']) && is_array($data['historyids'])
            ? array_map(static fn (mixed $id): string => (string) $id, $data['historyids'])
            : [];

        return new self(
            historyids: $historyids,
            response: isset($data['response']) && is_string($data['response'])
                ? $data['response']
                : '',
        );
    }

    /**
     * @return list<string>
     */
    public function getHistoryids(): array
    {
        return $this->historyids;
    }

    public function getResponse(): string
    {
        return $this->response;
    }

    public function isSuccess(): bool
    {
        return $this->response === 'success';
    }
}
