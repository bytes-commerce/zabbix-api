<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Tests;

use BytesCommerce\ZabbixApi\Zabbix\Trapper;
use BytesCommerce\ZabbixApi\Zabbix\ZabbixApiException;
use PHPUnit\Framework\TestCase;

final class TrapperTest extends TestCase
{
    private Trapper $trapper;

    protected function setUp(): void
    {
        // Use a dummy server since we're not actually connecting in tests
        $this->trapper = new Trapper('127.0.0.1', 10051, 1);
    }

    public function testSendSingleMetric(): void
    {
        $this->expectException(ZabbixApiException::class); // Will fail to connect to dummy server

        $this->trapper->send('test-host', 'test.key', 'test-value');
    }

    public function testSendBatchValid(): void
    {
        $this->expectException(ZabbixApiException::class); // Will fail to connect to dummy server

        $metrics = [
            [
                'host' => 'Linux-Server-01',
                'key' => 'custom.trapper.metric',
                'value' => '42'
            ],
            [
                'host' => 'Linux-Server-01',
                'key' => 'app.status',
                'value' => 'OK',
                'clock' => 1672531200
            ]
        ];

        $this->trapper->sendBatch($metrics);
    }

    public function testSendBatchInvalidMissingHost(): void
    {
        $metrics = [
            [
                'key' => 'test.key',
                'value' => 'test-value'
            ]
        ];

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Each metric must have host, key, and value');

        $this->trapper->sendBatch($metrics);
    }

    public function testSendBatchInvalidMissingKey(): void
    {
        $metrics = [
            [
                'host' => 'test-host',
                'value' => 'test-value'
            ]
        ];

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Each metric must have host, key, and value');

        $this->trapper->sendBatch($metrics);
    }

    public function testSendBatchInvalidMissingValue(): void
    {
        $metrics = [
            [
                'host' => 'test-host',
                'key' => 'test.key'
            ]
        ];

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Each metric must have host, key, and value');

        $this->trapper->sendBatch($metrics);
    }

    public function testBuildMessage(): void
    {
        $trapper = new Trapper('127.0.0.1');

        $reflection = new \ReflectionClass($trapper);
        $method = $reflection->getMethod('buildMessage');
        $method->setAccessible(true);

        $json = '{"request":"sender data","data":[{"host":"test","key":"test","value":"test"}]}';
        $message = $method->invoke($trapper, $json);

        // Check header
        $header = substr($message, 0, 5);
        self::assertSame("ZBXD\x01", $header);

        // Check length (should be 8 bytes)
        $lengthPart = substr($message, 5, 8);
        self::assertSame(8, strlen($lengthPart));

        // Check JSON part
        $jsonPart = substr($message, 13);
        self::assertSame($json, $jsonPart);
    }

    public function testParseInfoString(): void
    {
        $trapper = new Trapper('127.0.0.1');

        $reflection = new \ReflectionClass($trapper);
        $method = $reflection->getMethod('parseInfoString');
        $method->setAccessible(true);

        $info = 'processed: 2; failed: 0; total: 2; seconds spent: 0.000045';
        $result = $method->invoke($trapper, $info);

        self::assertSame([
            'processed' => 2,
            'failed' => 0,
            'total' => 2,
            'seconds_spent' => 0.000045
        ], $result);
    }

    public function testParseInfoStringPartial(): void
    {
        $trapper = new Trapper('127.0.0.1');

        $reflection = new \ReflectionClass($trapper);
        $method = $reflection->getMethod('parseInfoString');
        $method->setAccessible(true);

        $info = 'processed: 1; failed: 1';
        $result = $method->invoke($trapper, $info);

        self::assertSame([
            'processed' => 1,
            'failed' => 1
        ], $result);
    }
}
