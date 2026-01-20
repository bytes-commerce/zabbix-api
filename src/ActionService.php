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

        return $this->zabbixClient->call($action, $params);
    }
}
