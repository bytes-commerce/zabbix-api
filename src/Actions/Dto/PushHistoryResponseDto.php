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
        $historyids = [];
        if (isset($data['historyids']) && is_array($data['historyids'])) {
            foreach ($data['historyids'] as $id) {
                if (is_string($id)) {
                    $historyids[] = $id;
                } elseif (is_int($id)) {
                    $historyids[] = (string) $id;
                }
            }
        }

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
