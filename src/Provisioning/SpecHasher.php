<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Provisioning;

use BytesCommerce\ZabbixApi\Provisioning\ValueObject\DefinitionHash;

final readonly class SpecHasher
{
    public function hash(array $definition): DefinitionHash
    {
        $canonical = $this->canonicalize($definition);

        return DefinitionHash::fromData($canonical);
    }

    private function canonicalize(array $data): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            if (\is_array($value)) {
                $result[$key] = $this->canonicalize($value);
            } elseif (\is_string($value)) {
                $result[$key] = $this->normalizeString($value);
            } else {
                $result[$key] = $value;
            }
        }

        ksort($result);

        return $result;
    }

    private function normalizeString(string $value): string
    {
        return trim($value);
    }
}
