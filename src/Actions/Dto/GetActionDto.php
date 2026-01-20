<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Actions\Dto;

use Webmozart\Assert\Assert;

final readonly class GetActionDto
{
    public function __construct(
        public ?array $actionids = null,
        public ?array $groupids = null,
        public ?array $hostids = null,
        public ?array $triggerids = null,
        public ?array $mediatypeids = null,
        public ?array $usrgrpids = null,
        public ?array $userids = null,
        public ?array $scriptids = null,
        public ?string $selectConditions = null,
        public ?string $selectOperations = null,
        public string|array|null $output = null,
        public ?array $filter = null,
    ) {
        if ($actionids !== null) {
            Assert::allString($actionids);
        }
        if ($groupids !== null) {
            Assert::allString($groupids);
        }
        if ($hostids !== null) {
            Assert::allString($hostids);
        }
        if ($triggerids !== null) {
            Assert::allString($triggerids);
        }
        if ($mediatypeids !== null) {
            Assert::allString($mediatypeids);
        }
        if ($usrgrpids !== null) {
            Assert::allString($usrgrpids);
        }
        if ($userids !== null) {
            Assert::allString($userids);
        }
        if ($scriptids !== null) {
            Assert::allString($scriptids);
        }
        if ($selectConditions !== null) {
            Assert::string($selectConditions);
        }
        if ($selectOperations !== null) {
            Assert::string($selectOperations);
        }
        if ($output !== null) {
            Assert::string($output) || Assert::isArray($output);
        }
        if ($filter !== null) {
            Assert::isArray($filter);
        }
    }
}
