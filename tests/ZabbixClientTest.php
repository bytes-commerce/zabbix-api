<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Tests;

use BytesCommerce\ZabbixApi\Enums\ZabbixAction;
use BytesCommerce\ZabbixApi\ZabbixApiException;
use BytesCommerce\ZabbixApi\ZabbixClient;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class ZabbixClientTest extends TestCase
{
    private HttpClientInterface $httpClient;

    private LoggerInterface $logger;

    private CacheInterface $cache;

    private ZabbixClient $zabbixClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->cache = $this->createMock(CacheInterface::class);

        // Use API token auth for simple test cases (bypasses cache/login flow)
        $this->zabbixClient = new ZabbixClient(
            username: null,
            password: null,
            apiToken: 'test-api-token',
            httpClient: $this->httpClient,
            logger: $this->logger,
            cache: $this->cache,
            authTtl: 3600,
        );
    }

    public function testCallSuccess(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->expects(self::once())
            ->method('toArray')
            ->willReturn(['result' => ['test' => 'result']]);

        $this->httpClient->expects(self::once())
            ->method('request')
            ->with('POST', '', self::callback(function (array $options): bool {
                return $options['json']['jsonrpc'] === '2.0'
                    && $options['json']['method'] === 'host.get'
                    && $options['json']['params'] === ['param1' => 'value1']
                    && $options['json']['id'] === 1
                    && $options['headers']['Authorization'] === 'Bearer test-api-token';
            }))
            ->willReturn($response);

        $result = $this->zabbixClient->call(ZabbixAction::HOST_GET, ['param1' => 'value1']);

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

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Invalid params');

        $this->zabbixClient->call(ZabbixAction::HOST_GET);
    }

    public function testCallWithHttpError(): void
    {
        $this->httpClient->expects(self::once())
            ->method('request')
            ->willThrowException(new \Exception('Network error'));

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('HTTP request failed: Network error');

        $this->zabbixClient->call(ZabbixAction::HOST_GET);
    }

    public function testCallWithUsernamePasswordAuth(): void
    {
        $client = new ZabbixClient(
            username: 'testuser',
            password: 'testpass',
            apiToken: null,
            httpClient: $this->httpClient,
            logger: $this->logger,
            cache: $this->cache,
            authTtl: 3600,
        );

        // Mock cache->get() to simulate login and return a token
        $this->cache->expects(self::once())
            ->method('get')
            ->with('zabbix_bearer_token', self::anything())
            ->willReturnCallback(function (string $key, callable $callback): string {
                $item = $this->createMock(ItemInterface::class);
                $item->expects(self::once())->method('expiresAfter')->with(3600);

                return $callback($item);
            });

        // Expect two HTTP calls: one for login, one for the actual API call
        $loginResponse = $this->createMock(ResponseInterface::class);
        $loginResponse->method('toArray')->willReturn(['result' => 'auth-token-123']);

        $apiResponse = $this->createMock(ResponseInterface::class);
        $apiResponse->method('toArray')->willReturn(['result' => ['hostid' => '1']]);

        $this->httpClient->expects(self::exactly(2))
            ->method('request')
            ->willReturnOnConsecutiveCalls($loginResponse, $apiResponse);

        $result = $client->call(ZabbixAction::HOST_GET, ['output' => 'extend']);

        self::assertSame(['hostid' => '1'], $result);
    }

    public function testLoginRequestSkipsAuth(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->expects(self::once())
            ->method('toArray')
            ->willReturn(['result' => 'login-token']);

        $this->httpClient->expects(self::once())
            ->method('request')
            ->with('POST', '', self::callback(function (array $options): bool {
                return $options['json']['method'] === 'user.login'
                    && !isset($options['headers']['Authorization']);
            }))
            ->willReturn($response);

        $result = $this->zabbixClient->call(
            ZabbixAction::USER_LOGIN,
            ['username' => 'admin', 'password' => 'pass'],
        );

        self::assertSame('login-token', $result);
    }
}
