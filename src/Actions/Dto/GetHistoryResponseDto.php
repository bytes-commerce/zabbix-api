<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class GetHistoryResponseDto
{
    /**
     * @param list<HistoryDto> $history
     */
    public function __construct(
        public array $history,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $history = [];
        foreach ($data as $item) {
            if (is_array($item)) {
                $history[] = HistoryDto::fromArray($item);
            }
        }

        return new self(history: $history);
    }

    /**
     * @return list<HistoryDto>
     */
    public function getHistory(): array
    {
        return $this->history;
    }
}
