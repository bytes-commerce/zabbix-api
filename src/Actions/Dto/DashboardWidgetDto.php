<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

use Webmozart\Assert\Assert;

final readonly class DashboardWidgetDto
{
    /**
     * @param array<string, mixed>|null $fields
     */
    public function __construct(
        public string $type,
        public ?string $name,
        public ?int $x,
        public ?int $y,
        public ?int $width,
        public ?int $height,
        public ?array $fields,
        public ?string $view_mode,
    ) {
    }

    public static function fromArray(array $data): self
    {
        Assert::string($data['type'] ?? null);

        $x = null;
        if (isset($data['x'])) {
            Assert::integerish($data['x']);
            $x = (int) $data['x'];
        }

        $y = null;
        if (isset($data['y'])) {
            Assert::integerish($data['y']);
            $y = (int) $data['y'];
        }

        $width = null;
        if (isset($data['width'])) {
            Assert::integerish($data['width']);
            $width = (int) $data['width'];
        }

        $height = null;
        if (isset($data['height'])) {
            Assert::integerish($data['height']);
            $height = (int) $data['height'];
        }

        return new self(
            type: $data['type'],
            name: isset($data['name']) && is_string($data['name']) ? $data['name'] : null,
            x: $x,
            y: $y,
            width: $width,
            height: $height,
            fields: isset($data['fields']) && is_array($data['fields']) ? self::normalizeFields($data['fields']) : null,
            view_mode: isset($data['view_mode']) && is_string($data['view_mode']) ? $data['view_mode'] : null,
        );
    }

    /**
     * @param array<mixed, mixed> $fields
     * @return array<string, mixed>
     */
    private static function normalizeFields(array $fields): array
    {
        $normalized = [];
        foreach ($fields as $key => $value) {
            if (is_string($key)) {
                $normalized[$key] = $value;
            }
        }
        return $normalized;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getX(): ?int
    {
        return $this->x;
    }

    public function getY(): ?int
    {
        return $this->y;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getFields(): ?array
    {
        return $this->fields;
    }

    public function getViewMode(): ?string
    {
        return $this->view_mode;
    }
}
