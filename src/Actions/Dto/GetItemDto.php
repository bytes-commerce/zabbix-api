<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

use Webmozart\Assert\Assert;

final readonly class GetItemDto
{
    public function __construct(
        public ?array $itemids = null,
        public ?array $groupids = null,
        public ?array $hostids = null,
        public ?array $interfaceids = null,
        public ?array $templateids = null,
        public ?bool $inherited = null,
        public ?bool $templated = null,
        public ?bool $monitored = null,
        public string|array|null $output = null,
        public ?string $selectHosts = null,
        public ?string $selectTriggers = null,
        public ?string $selectPreprocessing = null,
        public ?string $selectTags = null,
        public ?array $filter = null,
        public ?array $search = null,
        public ?string $sortfield = null,
    ) {
        if ($itemids !== null) {
            Assert::allString($itemids);
        }
        if ($groupids !== null) {
            Assert::allString($groupids);
        }
        if ($hostids !== null) {
            Assert::allString($hostids);
        }
        if ($interfaceids !== null) {
            Assert::allString($interfaceids);
        }
        if ($templateids !== null) {
            Assert::allString($templateids);
        }
        if ($inherited !== null) {
            Assert::boolean($inherited);
        }
        if ($templated !== null) {
            Assert::boolean($templated);
        }
        if ($monitored !== null) {
            Assert::boolean($monitored);
        }
        if ($output !== null) {
            Assert::string($output) || Assert::isArray($output);
        }
        if ($selectHosts !== null) {
            Assert::string($selectHosts);
        }
        if ($selectTriggers !== null) {
            Assert::string($selectTriggers);
        }
        if ($selectPreprocessing !== null) {
            Assert::string($selectPreprocessing);
        }
        if ($selectTags !== null) {
            Assert::string($selectTags);
        }
        if ($filter !== null) {
            Assert::isArray($filter);
        }
        if ($search !== null) {
            Assert::isArray($search);
        }
        if ($sortfield !== null) {
            Assert::string($sortfield);
        }
    }
}
