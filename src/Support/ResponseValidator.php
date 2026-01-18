<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Support;

use BytesCommerce\Zabbix\ZabbixApiException;
use Webmozart\Assert\Assert;

/**
 * Validates Zabbix API responses with strict type-safety
 */
final class ResponseValidator
{
    /**
     * @return array<string, mixed>
     *
     * @throws ZabbixApiException
     */
    public static function ensureArray(mixed $result): array
    {
        if (!is_array($result)) {
            throw new ZabbixApiException(
                sprintf('Expected array response, got %s', get_debug_type($result)),
                -1,
            );
        }

        return $result;
    }

    /**
     * @return list<array<string, mixed>>
     *
     * @throws ZabbixApiException
     */
    public static function ensureArrayOfArrays(mixed $result): array
    {
        $validated = self::ensureArray($result);
        Assert::isList($validated, 'Response must be a list');
        Assert::allIsArray($validated, 'All items must be arrays');

        return $validated;
    }

    /**
     * @throws ZabbixApiException
     */
    public static function ensureString(mixed $result): string
    {
        if (!is_string($result)) {
            throw new ZabbixApiException(
                sprintf('Expected string response, got %s', get_debug_type($result)),
                -1,
            );
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws ZabbixApiException
     */
    public static function ensureArrayKey(array $data, string $key): mixed
    {
        if (!array_key_exists($key, $data)) {
            throw new ZabbixApiException(
                sprintf('Missing required key "%s" in response', $key),
                -1,
            );
        }

        return $data[$key];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string>
     *
     * @throws ZabbixApiException
     */
    public static function ensureStringArray(array $data, string $key): array
    {
        $value = self::ensureArrayKey($data, $key);

        if (!is_array($value)) {
            throw new ZabbixApiException(
                sprintf('Expected array for key "%s", got %s', $key, get_debug_type($value)),
                -1,
            );
        }

        Assert::allString($value, sprintf('All values in "%s" must be strings', $key));

        return $value;
    }

    /**
     * @param array<string, mixed> $error
     *
     * @return array{message: string, code: int, data: string|null}
     */
    public static function ensureErrorStructure(array $error): array
    {
        return [
            'message' => isset($error['message']) && is_string($error['message']) 
                ? $error['message'] 
                : 'Unknown Zabbix API error',
            'code' => isset($error['code']) && is_int($error['code']) 
                ? $error['code'] 
                : -1,
            'data' => isset($error['data']) && is_string($error['data']) 
                ? $error['data'] 
                : null,
        ];
    }
}
