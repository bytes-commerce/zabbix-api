<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix;

use BytesCommerce\Zabbix\Enums\ZabbixAction;
use BytesCommerce\Zabbix\Support\ResponseValidator;
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
            $result = $this->executeApiCall($action, $params, $authToken);

            return $result;
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
        $token = $this->cache->get(self::CACHE_KEY, function (ItemInterface $item): ?string {
            $item->expiresAfter(null);

            if ($this->username === null || $this->password === null) {
                return null;
            }

            return $this->performLogin();
        });

        return $token === '' ? null : $token;
    }

    private function performLogin(): string
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

        $this->cache->get(self::CACHE_KEY, function (ItemInterface $item) use ($result): string {
            $item->expiresAfter($this->authTtl);

            return $result;
        });

        $this->logger->info('Zabbix authentication successful', [
            'username' => $this->username,
            'ttl' => $this->authTtl,
        ]);

        return $result;
    }

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
                $error = ResponseValidator::ensureErrorStructure($data['error']);
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
