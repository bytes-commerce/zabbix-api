<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

use BytesCommerce\ZabbixApi\Enums\StatusEnum;
use Webmozart\Assert\Assert;

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
        Assert::string($data['triggerid'] ?? null);
        Assert::string($data['description'] ?? null);
        Assert::string($data['expression'] ?? null);

        $priority = null;
        if (isset($data['priority'])) {
            Assert::integerish($data['priority']);
            $priority = (int) $data['priority'];
        }

        $status = null;
        if (isset($data['status'])) {
            Assert::integerish($data['status']);
            $status = StatusEnum::from((int) $data['status']);
        }

        $type = null;
        if (isset($data['type'])) {
            Assert::integerish($data['type']);
            $type = (int) $data['type'];
        }

        return new self(
            triggerid: $data['triggerid'],
            description: $data['description'],
            expression: $data['expression'],
            priority: $priority,
            status: $status,
            comments: isset($data['comments']) && is_string($data['comments']) ? $data['comments'] : null,
            type: $type,
            dependencies: isset($data['dependencies']) && is_array($data['dependencies']) ? $data['dependencies'] : null,
            tags: isset($data['tags']) && is_array($data['tags']) ? $data['tags'] : null,
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
