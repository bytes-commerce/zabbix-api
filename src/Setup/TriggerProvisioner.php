<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Setup;

use BytesCommerce\ZabbixApi\Actions\Trigger;
use BytesCommerce\ZabbixApi\Contract\ZabbixNamingProviderInterface;
use BytesCommerce\ZabbixApi\Enums\ZabbixAction;
use Psr\Log\LoggerInterface;
use Webmozart\Assert\Assert;

final readonly class TriggerProvisioner
{
    private const int PRIORITY_WARNING = 3;
    private const int PRIORITY_HIGH = 4;
    private const int PRIORITY_AVERAGE = 2;

    public function __construct(
        private Trigger $triggerAction,
        private ZabbixNamingProviderInterface $naming,
        private LoggerInterface $logger,
    ) {
    }

    public function provisionTriggers(string $hostId, string $hostName): void
    {
        $triggerDefinitions = $this->getTriggerDefinitions($hostId, $hostName);

        foreach ($triggerDefinitions as $triggerDef) {
            if (is_array($triggerDef)) {
                $this->ensureTrigger($triggerDef, $hostId);
            }
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getTriggerDefinitions(string $hostId, string $hostName): array
    {
        $durationKey = $this->naming->getItemKey('tx.duration_ms');
        $statusKey = $this->naming->getItemKey('tx.http_status');
        $loginFailureKey = $this->naming->getItemKey('auth.login.failure');
        $exceptionKey = $this->naming->getItemKey('error.exception');
        $messengerFailedKey = $this->naming->getItemKey('messenger.failed.count');

        return [
            [
                'description' => 'High response time on ' . $hostName,
                'expression' => sprintf('last(/%s/%s) > 2000', $hostName, $durationKey),
                'priority' => self::PRIORITY_WARNING,
                'tags' => [
                    ['tag' => 'slo', 'value' => 'latency'],
                    ['tag' => 'class', 'value' => 'performance'],
                ],
            ],
            [
                'description' => 'Critical response time on ' . $hostName,
                'expression' => sprintf('last(/%s/%s) > 5000', $hostName, $durationKey),
                'priority' => self::PRIORITY_HIGH,
                'tags' => [
                    ['tag' => 'slo', 'value' => 'latency'],
                    ['tag' => 'class', 'value' => 'performance'],
                ],
            ],
            [
                'description' => 'HTTP 5xx error detected on ' . $hostName,
                'expression' => sprintf('last(/%s/%s) >= 500', $hostName, $statusKey),
                'priority' => self::PRIORITY_HIGH,
                'tags' => [
                    ['tag' => 'slo', 'value' => 'availability'],
                    ['tag' => 'class', 'value' => 'error'],
                ],
            ],
            [
                'description' => 'HTTP 4xx error detected on ' . $hostName,
                'expression' => sprintf('last(/%s/%s) >= 400 and last(/%s/%s) < 500', $hostName, $statusKey, $hostName, $statusKey),
                'priority' => self::PRIORITY_AVERAGE,
                'tags' => [
                    ['tag' => 'class', 'value' => 'client-error'],
                ],
            ],
            [
                'description' => 'Login failure spike detected on ' . $hostName,
                'expression' => sprintf('avg(/%s/%s,5m) > 10', $hostName, $loginFailureKey),
                'priority' => self::PRIORITY_HIGH,
                'tags' => [
                    ['tag' => 'security', 'value' => 'brute-force'],
                    ['tag' => 'class', 'value' => 'security'],
                ],
            ],
            [
                'description' => 'Exception rate high on ' . $hostName,
                'expression' => sprintf('count(/%s/%s,5m) > 5', $hostName, $exceptionKey),
                'priority' => self::PRIORITY_WARNING,
                'tags' => [
                    ['tag' => 'class', 'value' => 'error'],
                ],
            ],
            [
                'description' => 'Failed messages in queue on ' . $hostName,
                'expression' => sprintf('last(/%s/%s) > 0', $hostName, $messengerFailedKey),
                'priority' => self::PRIORITY_WARNING,
                'tags' => [
                    ['tag' => 'class', 'value' => 'messenger'],
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $triggerDef
     */
    private function ensureTrigger(array $triggerDef, string $hostId): void
    {
        $existingTriggers = $this->triggerAction->get([
            'hostids' => [$hostId],
            'filter' => ['description' => $triggerDef['description']],
            'output' => ['triggerid'],
        ]);

        if (count($existingTriggers->triggers) > 0) {
            $this->logger->debug('Trigger already exists', ['description' => $triggerDef['description']]);

            return;
        }

        try {
            $result = $this->triggerAction->create([$triggerDef]);

            if (is_array($result)) {
                Assert::keyExists($result, 'triggerids', 'Failed to create trigger');

                $triggerIds = $result['triggerids'];
                $triggerId = is_array($triggerIds) && isset($triggerIds[0]) ? $triggerIds[0] : null;

                $this->logger->info('Trigger created', [
                    'description' => $triggerDef['description'],
                    'triggerid' => $triggerId,
                ]);
            }
        } catch (\Throwable $e) {
            $this->logger->error('Failed to create trigger', [
                'description' => $triggerDef['description'],
                'error' => $e->getMessage(),
            ]);
        }
    }
}
