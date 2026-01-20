<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Tests\Unit\Zabbix;

use BytesCommerce\ZabbixApi\ZabbixApiException;
use BytesCommerce\ZabbixApi\ZabbixClient;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class ZabbixClientTest extends TestCase
{
    private HttpClientInterface $httpClient;

    private LoggerInterface $logger;

    private ZabbixClient $zabbixClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->zabbixClient = new ZabbixClient($this->httpClient, $this->logger);
    }

    public function testCallSuccess(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->expects(self::once())
            ->method('toArray')
            ->willReturn(['result' => ['test' => 'result']]);

        $this->httpClient->expects(self::once())
            ->method('request')
            ->with('POST', '', [
                'json' => [
                    'jsonrpc' => '2.0',
                    'method' => 'test.method',
                    'params' => ['param1' => 'value1'],
                    'id' => 1,
                ],
            ])
            ->willReturn($response);

        $this->logger->expects(self::once())
            ->method('debug')
            ->with('Zabbix API call', self::anything());

        $result = $this->zabbixClient->call('test.method', ['param1' => 'value1']);

        self::assertSame(['test' => 'result'], $result);
    }

    public function testCallWithError(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->expects(self::once())
            ->method('toArray')
            ->willReturn([
                'error' => [
                    'code' => -32602,
                    'message' => 'Invalid params',
                    'data' => 'Invalid parameter',
                ],
            ]);

        $this->httpClient->expects(self::once())
            ->method('request')
            ->willReturn($response);

        $this->logger->expects(self::once())
            ->method('debug');

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Invalid params');

        $this->zabbixClient->call('test.method');
    }

    public function testCallWithHttpError(): void
    {
        $this->httpClient->expects(self::once())
            ->method('request')
            ->willThrowException(new \Exception('Network error'));

        $this->logger->expects(self::once())
            ->method('debug');
        $this->logger->expects(self::once())
            ->method('error')
            ->with('Zabbix API call failed', self::anything());

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('HTTP request failed: Network error');

        $this->zabbixClient->call('test.method');
    }
}
