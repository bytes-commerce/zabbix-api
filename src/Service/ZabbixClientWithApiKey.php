<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Service;

use BytesCommerce\ZabbixApi\Contract\ZabbixClientWithApiKeyInterface;
use BytesCommerce\ZabbixApi\Enums\ZabbixAction;
use BytesCommerce\ZabbixApi\Support\ResponseValidator;
use BytesCommerce\ZabbixApi\ZabbixApiException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

final class ZabbixClientWithApiKey implements ZabbixClientWithApiKeyInterface
{
    private const string CACHE_KEY = 'zabbix_bearer_token';

    private const array AUTH_ERROR_MESSAGES = [
        'Session terminated',
        'Not authorized',
        'Not authorised',
        'Session has expired',
    ];

    private int $requestId = 0;

    public function __construct(
        #[Autowire('%zabbix_api.api_token%')]
        private readonly ?string $apiToken,
        #[Autowire('%zabbix_api.username%')]
        private readonly ?string $username,
        #[Autowire('%zabbix_api.password%')]
        private readonly ?string $password,
        #[Autowire(service: 'zabbix.http_client')]
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        private readonly CacheInterface $cache,
        #[Autowire('%zabbix_api.auth_ttl%')]
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
                    'errorData' => $e->getErrorData(),
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

            if ($this->apiToken !== null && $this->apiToken !== '') {
                return $this->apiToken;
            }

            if ($this->username === null || $this->password === null) {
                return null;
            }

            return $this->performLogin();
        });

        return $token === '' ? null : $token;
    }

    private function performLogin(): string
    {
        if ($this->apiToken !== null && $this->apiToken !== '') {
            return $this->apiToken;
        }

        if (!$this->username || !$this->password) {
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

        if (!\is_string($result)) {
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
        $requestBody = [
            'jsonrpc' => '2.0',
            'method' => $action->value,
            'params' => $params,
            'id' => ++$this->requestId,
        ];

        $headers = [
            'Content-Type' => 'application/json-rpc',
        ];

        if ($authToken !== null) {
            $headers['Authorization'] = \sprintf('Bearer %s', $authToken);
        }

        $this->logger->debug('Zabbix API call', [
            'method' => $action->value,
            'id' => $requestBody['id'],
            'authenticated' => $authToken !== null,
        ]);

        try {
            $response = $this->httpClient->request('POST', '', [
                'json' => $requestBody,
                'headers' => $headers,
            ]);

            $data = $response->toArray();

            if (isset($data['error']) && \is_array($data['error'])) {
                $error = ResponseValidator::ensureErrorStructure($data['error']);

                throw new ZabbixApiException(
                    $error['message'],
                    $error['code'],
                    $error['data'],
                );
            }

            return $data['result'] ?? null;
        } catch (Throwable $e) {
            $this->logger->error('Zabbix API call failed', [
                'method' => $action->value,
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
        return array_any(self::AUTH_ERROR_MESSAGES, static fn ($message) => str_contains($exception->getMessage(), $message));
    }
}
