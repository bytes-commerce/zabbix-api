<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

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
        return new self(
            type: $data['type'],
            name: $data['name'] ?? null,
            x: isset($data['x']) ? (int) $data['x'] : null,
            y: isset($data['y']) ? (int) $data['y'] : null,
            width: isset($data['width']) ? (int) $data['width'] : null,
            height: isset($data['height']) ? (int) $data['height'] : null,
            fields: $data['fields'] ?? null,
            view_mode: $data['view_mode'] ?? null,
        );
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
