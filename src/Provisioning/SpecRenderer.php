<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Provisioning;

use BytesCommerce\ZabbixApi\Provisioning\Dto\HostInfo;

final readonly class SpecRenderer
{
    public function renderTitleTemplate(string $template, HostInfo $host): string
    {
        $replacements = [
            '{{ host.name }}' => $host->name,
            '{{ host.id }}' => $host->hostId,
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template,
        );
    }

    public function renderWidgets(array $widgets, HostInfo $host): array
    {
        $replacements = [
            '{{ host.id }}' => $host->hostId,
            '{{ host.name }}' => $host->name,
        ];

        return $this->renderArray($widgets, $replacements);
    }

    private function renderArray(array $data, array $replacements): array
    {
        $result = [];

        $search = array_keys($replacements);
        $replace = array_values($replacements);

        $searchStrings = [];
        foreach ($search as $s) {
            if (is_string($s)) {
                $searchStrings[] = $s;
            }
        }

        $replaceStrings = [];
        foreach ($replace as $r) {
            if (is_string($r)) {
                $replaceStrings[] = $r;
            }
        }

        foreach ($data as $key => $value) {
            if (\is_array($value)) {
                $result[$key] = $this->renderArray($value, $replacements);
            } elseif (\is_string($value)) {
                $result[$key] = str_replace(
                    $searchStrings,
                    $replaceStrings,
                    $value,
                );
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
