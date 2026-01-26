<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class GetHistoryResponseDto
{
    /**
     * @param HistoryDto[] $history
     */
    public function __construct(
        public array $history,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            history: array_map(HistoryDto::fromArray(...), $data),
        );
    }

    /**
     * @return HistoryDto[]
     */
    public function getHistory(): array
    {
        return $this->history;
    }
}
