<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

use BytesCommerce\ZabbixApi\Enums\StatusEnum;

final readonly class TriggerDto
{
    public function __construct(
        public string $triggerid,
        public string $description,
        public string $expression,
        public ?int $priority,
        public ?StatusEnum $status,
        public ?string $comments,
        public ?int $type,
        public ?array $dependencies,
        public ?array $tags,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            triggerid: $data['triggerid'],
            description: $data['description'],
            expression: $data['expression'],
            priority: $data['priority'] ?? null,
            status: isset($data['status']) ? StatusEnum::from($data['status']) : null,
            comments: $data['comments'] ?? null,
            type: $data['type'] ?? null,
            dependencies: $data['dependencies'] ?? null,
            tags: $data['tags'] ?? null,
        );
    }

    public function getTriggerid(): string
    {
        return $this->triggerid;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getExpression(): string
    {
        return $this->expression;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function getStatus(): ?StatusEnum
    {
        return $this->status;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function getDependencies(): ?array
    {
        return $this->dependencies;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }
}
