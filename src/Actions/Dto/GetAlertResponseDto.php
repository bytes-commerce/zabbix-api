<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

final readonly class GetAlertResponseDto
{
    /**
     * @param list<AlertDto> $alerts
     */
    public function __construct(
        public array $alerts,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $alerts = [];
        foreach ($data as $item) {
            if (is_array($item)) {
                $alerts[] = AlertDto::fromArray($item);
            }
        }

        return new self(alerts: $alerts);
    }
}
