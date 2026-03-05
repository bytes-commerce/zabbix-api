<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Setup;

use BytesCommerce\ZabbixApi\Contract\ItemDefinitionProviderInterface;
use BytesCommerce\ZabbixApi\Contract\ZabbixSenderInterface;
use BytesCommerce\ZabbixApi\Contract\ZabbixSetupInterface;
use BytesCommerce\ZabbixApi\Enums\ZabbixAction;
use BytesCommerce\ZabbixApi\ZabbixClientInterface;
use JsonException;
use Psr\Log\LoggerInterface;
use Throwable;

final readonly class ZabbixSender implements ZabbixSenderInterface
{
    public function __construct(
        private ZabbixClientInterface $client,
        private ZabbixSetupInterface $setup,
        private ItemDefinitionProviderInterface $registry,
        private LoggerInterface $logger,
    ) {
    }

    public function pushNumeric(string $key, float|int $value, int $clock, array $tags, string $correlationId): void
    {
        $this->setup->ensureFast();

        $itemId = $this->registry->getItemIdForKey($key);
        if ($itemId === null) {
            $this->logger->warning('Zabbix item not found', ['key' => $key]);

            return;
        }

        try {
            $this->client->call(ZabbixAction::HISTORY_PUSH, [
                [
                    'itemid' => $itemId,
                    'clock' => $clock,
                    'value' => (string) $value,
                ],
            ]);
        } catch (Throwable $e) {
            $this->logger->error('Failed to push metric to Zabbix', [
                'key' => $key,
                'value' => $value,
                'error' => $e->getMessage(),
                'correlationId' => $correlationId,
            ]);
        }
    }

    public function pushEvent(string $key, array $payload, int $clock, string $correlationId): void
    {
        $this->setup->ensureFast();

        $itemId = $this->registry->getItemIdForKey($key);
        if ($itemId === null) {
            $this->logger->warning('Zabbix item not found', ['key' => $key]);

            return;
        }

        try {
            $jsonPayload = json_encode($payload, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);

            $this->client->call(ZabbixAction::HISTORY_PUSH, [
                [
                    'itemid' => $itemId,
                    'clock' => $clock,
                    'value' => $jsonPayload,
                ],
            ]);
        } catch (JsonException $e) {
            $this->logger->error('Failed to encode event payload', [
                'key' => $key,
                'error' => $e->getMessage(),
                'correlationId' => $correlationId,
            ]);
        } catch (Throwable $e) {
            $this->logger->error('Failed to push event to Zabbix', [
                'key' => $key,
                'error' => $e->getMessage(),
                'correlationId' => $correlationId,
            ]);
        }
    }
}
