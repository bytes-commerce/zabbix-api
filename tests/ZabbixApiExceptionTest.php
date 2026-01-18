<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Tests\Unit\Zabbix;

use BytesCommerce\ZabbixApi\Zabbix\ZabbixApiException;
use PHPUnit\Framework\TestCase;

final class ZabbixApiExceptionTest extends TestCase
{
    public function testExceptionCreation(): void
    {
        $exception = new ZabbixApiException('Test error', 123, 'Test data');

        self::assertSame('Test error', $exception->getMessage());
        self::assertSame(123, $exception->getErrorCode());
        self::assertSame('Test data', $exception->getErrorData());
    }

    public function testExceptionCreationWithoutData(): void
    {
        $exception = new ZabbixApiException('Test error', 123);

        self::assertSame('Test error', $exception->getMessage());
        self::assertSame(123, $exception->getErrorCode());
        self::assertNull($exception->getErrorData());
    }
}
