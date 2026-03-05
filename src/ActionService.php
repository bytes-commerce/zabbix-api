<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi;

use BytesCommerce\ZabbixApi\Actions\AbstractAction;
use BytesCommerce\ZabbixApi\Enums\ZabbixAction;

final readonly class ActionService implements ActionServiceInterface
{
    public function __construct(
        private ZabbixClientInterface $zabbixClient,
    ) {
    }

    /**
     * @param class-string<AbstractAction> $actionClass
     * @param array<string, mixed> $input
     *
     * @throws ZabbixApiException
     */
    public function call(string $actionClass, array $input): mixed
    {
        if (!is_subclass_of($actionClass, AbstractAction::class)) {
            throw new ZabbixApiException(
                sprintf('Class %s must extend %s', $actionClass, AbstractAction::class),
                -1,
            );
        }

        $base = $actionClass::getActionPrefix();
        $method = $input['method'] ?? 'get';
        $params = $input['params'] ?? $input;

        if (!is_string($method)) {
            throw new ZabbixApiException(
                'Method must be a string',
                -1,
            );
        }

        $actionString = sprintf('%s.%s', $base, $method);

        try {
            $action = ZabbixAction::from($actionString);
        } catch (\ValueError $e) {
            throw new ZabbixApiException(
                sprintf('Invalid action method: %s (available for %s)', $actionString, $base),
                -1,
                null,
                $e,
            );
        }

        if (!is_array($params)) {
            throw new ZabbixApiException(
                'Params must be an array',
                -1,
            );
        }

        $typedParams = [];
        foreach ($params as $key => $value) {
            if (is_string($key)) {
                $typedParams[$key] = $value;
            }
        }

        return $this->zabbixClient->call($action, $typedParams);
    }
}
