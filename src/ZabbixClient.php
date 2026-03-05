<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi;

use BytesCommerce\ZabbixApi\Enums\ZabbixAction;
use BytesCommerce\ZabbixApi\Support\ResponseValidator;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ZabbixClient implements ZabbixClientInterface
{
    private const string CACHE_KEY = 'zabbix_bearer_token';

    private const array AUTH_ERROR_CODES = [-32602, -32500];

    private const array AUTH_ERROR_MESSAGES = [
        'Session terminated',
        'Not authorized',
        'Not authorised',
        'Session has expired',
    ];

    private int $requestId = 0;

    public function __construct(
        private readonly ?string $username,
        private readonly ?string $password,
        private readonly ?string $apiToken,
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        private readonly CacheInterface $cache,
        private readonly int $authTtl,
    ) {
    }

    public function call(ZabbixAction $action, array $params = []): mixed
    {
        $isLoginRequest = $action === ZabbixAction::USER_LOGIN;

        if ($isLoginRequest) {
            return $this->executeApiCall($action, $params, null);
        }

        $authToken = $this->getAuthToken();

        try {
            return $this->executeApiCall($action, $params, $authToken);
        } catch (ZabbixApiException $e) {
            if ($this->isAuthFailure($e)) {
                $this->logger->info('Authentication failure detected, retrying with fresh token', [
                    'error' => $e->getMessage(),
                    'code' => $e->getErrorCode(),
                ]);

                $this->cache->delete(self::CACHE_KEY);
                $authToken = $this->performLogin();

                return $this->executeApiCall($action, $params, $authToken);
            }

            throw $e;
        }
    }

    private function getAuthToken(): ?string
    {
        // If an API token is configured, use it directly (no caching needed)
        if ($this->apiToken !== null && $this->apiToken !== '') {
            return $this->apiToken;
        }

        $authTtl = $this->authTtl;

        $token = $this->cache->get(self::CACHE_KEY, function (ItemInterface $item) use ($authTtl): ?string {
            $item->expiresAfter($authTtl);

            if ($this->username === null || $this->password === null) {
                return null;
            }

            return $this->doLogin();
        });

        return $token === '' ? null : $token;
    }

    private function performLogin(): string
    {
        $token = $this->doLogin();

        // Delete stale entry and re-store with proper TTL
        $this->cache->delete(self::CACHE_KEY);
        $authTtl = $this->authTtl;
        $this->cache->get(self::CACHE_KEY, function (ItemInterface $item) use ($token, $authTtl): string {
            $item->expiresAfter($authTtl);

            return $token;
        });

        $this->logger->info('Zabbix authentication successful', [
            'username' => $this->username,
            'ttl' => $this->authTtl,
        ]);

        return $token;
    }

    private function doLogin(): string
    {
        if ($this->username === null || $this->password === null) {
            throw new ZabbixApiException(
                'Username and password must be configured for authentication',
                -1,
            );
        }

        $this->logger->debug('Performing Zabbix login', ['username' => $this->username]);

        $result = $this->executeApiCall(
            ZabbixAction::USER_LOGIN,
            ['username' => $this->username, 'password' => $this->password],
            null,
        );

        if (!is_string($result)) {
            throw new ZabbixApiException('Invalid login response: expected string token', -1);
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $params
     */
    private function executeApiCall(ZabbixAction $action, array $params, ?string $authToken): mixed
    {
        $method = $action->value;
        $requestBody = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params,
            'id' => ++$this->requestId,
        ];

        $headers = [
            'Content-Type' => 'application/json-rpc',
        ];

        if ($authToken !== null) {
            $headers['Authorization'] = sprintf('Bearer %s', $authToken);
        }

        $this->logger->debug('Zabbix API call', [
            'method' => $method,
            'id' => $requestBody['id'],
            'authenticated' => $authToken !== null,
        ]);

        try {
            $response = $this->httpClient->request('POST', '', [
                'json' => $requestBody,
                'headers' => $headers,
            ]);

            $data = $response->toArray();

            if (isset($data['error']) && is_array($data['error'])) {
                $error = ResponseValidator::ensureErrorStructure(
                    ResponseValidator::ensureArray($data['error']),
                );
                throw new ZabbixApiException(
                    $error['message'],
                    $error['code'],
                    $error['data'],
                );
            }

            return $data['result'] ?? null;
        } catch (\Throwable $e) {
            $this->logger->error('Zabbix API call failed', [
                'method' => $method,
                'error' => $e->getMessage(),
            ]);

            if ($e instanceof ZabbixApiException) {
                throw $e;
            }

            throw new ZabbixApiException('HTTP request failed: ' . $e->getMessage(), -1, null, $e);
        }
    }

    private function isAuthFailure(ZabbixApiException $exception): bool
    {
        if (in_array($exception->getErrorCode(), self::AUTH_ERROR_CODES, true)) {
            return true;
        }

        foreach (self::AUTH_ERROR_MESSAGES as $message) {
            if (str_contains($exception->getMessage(), $message)) {
                return true;
            }
        }

        return false;
    }
}
