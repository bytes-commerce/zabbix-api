<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Setup;

use BytesCommerce\ZabbixApi\Contract\ItemDefinitionProviderInterface;
use BytesCommerce\ZabbixApi\Contract\ZabbixNamingProviderInterface;
use BytesCommerce\ZabbixApi\Contract\ZabbixSetupInterface;
use BytesCommerce\ZabbixApi\Enums\ZabbixAction;
use BytesCommerce\ZabbixApi\ZabbixApiException;
use BytesCommerce\ZabbixApi\ZabbixClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Webmozart\Assert\Assert;

final readonly class ZabbixSetup implements ZabbixSetupInterface
{
    public function __construct(
        private ZabbixClientInterface $client,
        private ZabbixNamingProviderInterface $naming,
        private ItemDefinitionProviderInterface $registry,
        private LoggerInterface $logger,
        #[Autowire('%zabbix_api.setup_enabled%')]
        private bool $setupEnabled,
        private ?TriggerProvisioner $triggerProvisioner = null,
    ) {
    }

    public function ensureFast(): void
    {
        if (!$this->setupEnabled) {
            return;
        }

        $hostId = $this->registry->getHostId();
        if ($hostId !== null) {
            return;
        }

        $this->ensureAll();
    }

    public function ensureAll(): void
    {
        if (!$this->setupEnabled) {
            return;
        }

        $hostId = $this->ensureHost();
        $this->registry->setHostId($hostId);

        $this->ensureItems($hostId);

        $this->ensureTriggers($hostId);
    }

    public function ensureHost(): string
    {
        $hostName = $this->naming->getHostName();
        $cleanName = $this->naming->getCleanHostName();

        $result = $this->client->call(ZabbixAction::HOST_GET, [
            'filter' => ['name' => $hostName, 'host' => $cleanName],
            'output' => ['hostid'],
        ]);

        Assert::isArray($result);
        if (count($result) > 0) {
            Assert::isArray($result[0]);
            $hostId = $result[0]['hostid'] ?? null;
            if (is_string($hostId) && $hostId !== '') {
                return $hostId;
            }
        }

        $groupId = $this->ensureHostGroup();

        $result = $this->client->call(ZabbixAction::HOST_CREATE, [
            'host' => $cleanName,
            'name' => $hostName,
            'groups' => [['groupid' => $groupId]],
            'interfaces' => [
                [
                    'type' => 1,
                    'main' => 1,
                    'useip' => 1,
                    'ip' => '127.0.0.1',
                    'dns' => '',
                    'port' => '10050',
                ],
            ],
            'tags' => [
                ['tag' => 'class', 'value' => 'software'],
                ['tag' => 'subclass', 'value' => 'web-application'],
                ['tag' => 'framework', 'value' => 'symfony'],
            ],
        ]);

        Assert::isArray($result);
        Assert::keyExists($result, 'hostids');
        Assert::isArray($result['hostids']);
        $hostId = $result['hostids'][0];
        Assert::string($hostId);
        $this->logger->info('Zabbix host created', ['host' => $hostName, 'hostid' => $hostId]);

        return $hostId;
    }

    private function ensureHostGroup(): string
    {
        $hostGroup = $this->naming->getHostGroup();

        $result = $this->client->call(ZabbixAction::HOSTGROUP_GET, [
            'filter' => ['name' => $hostGroup],
            'output' => ['groupid'],
        ]);

        Assert::isArray($result);
        if (count($result) > 0) {
            Assert::isArray($result[0]);
            $groupId = $result[0]['groupid'] ?? null;
            if (is_string($groupId) && $groupId !== '') {
                return $groupId;
            }
        }

        $result = $this->client->call(ZabbixAction::HOSTGROUP_CREATE, [
            'name' => $hostGroup,
        ]);

        Assert::isArray($result);
        Assert::keyExists($result, 'groupids', 'Failed to create Zabbix host group');
        Assert::isArray($result['groupids']);
        $groupId = $result['groupids'][0];
        Assert::string($groupId);
        $this->logger->info('Zabbix host group created', ['group' => $hostGroup, 'groupid' => $groupId]);

        return $groupId;
    }

    private function ensureItems(string $hostId): void
    {
        $itemDefinitions = $this->registry->getAllItemDefinitions();

        foreach ($itemDefinitions as $suffix => $definition) {
            Assert::isArray($definition);
            $key = $this->registry->getFullItemKey($suffix);

            $result = $this->client->call(ZabbixAction::ITEM_GET, [
                'hostids' => $hostId,
                'filter' => ['key_' => $key],
                'output' => ['itemid'],
            ]);

            Assert::isArray($result);
            if (count($result) > 0) {
                Assert::isArray($result[0]);
                $itemId = $result[0]['itemid'] ?? null;
                if (is_string($itemId) && $itemId !== '') {
                    $this->registry->setItemId($key, $itemId);
                    continue;
                }
            }

            try {
                Assert::keyExists($definition, 'name');
                Assert::keyExists($definition, 'type');
                Assert::keyExists($definition, 'value_type');
                Assert::keyExists($definition, 'history');

                $itemData = [
                    'name' => $definition['name'],
                    'key_' => $key,
                    'hostid' => $hostId,
                    'type' => $definition['type'],
                    'value_type' => $definition['value_type'],
                    'history' => $definition['history'],
                ];

                if (isset($definition['units'])) {
                    $itemData['units'] = $definition['units'];
                }

                $result = $this->client->call(ZabbixAction::ITEM_CREATE, $itemData);
            } catch (ZabbixApiException $e) {
                $this->logger->error('Failed to create Zabbix item', [
                    'key' => $key,
                    'error' => $e->getMessage(),
                    'errorData' => $e->getErrorData(),
                ]);

                continue;
            }

            Assert::isArray($result);
            Assert::keyExists($result, 'itemids', sprintf('Failed to create Zabbix item, expected key "itemids" in response, got %s', implode(',', array_keys($result))));
            Assert::isArray($result['itemids']);
            $itemId = $result['itemids'][0];
            Assert::string($itemId);
            $this->registry->setItemId($key, $itemId);
            $this->logger->info('Zabbix item created', ['key' => $key, 'itemid' => $itemId]);
        }
    }

    private function ensureTriggers(string $hostId): void
    {
        if ($this->triggerProvisioner === null) {
            return;
        }

        $hostName = $this->naming->getHostName();
        $this->triggerProvisioner->provisionTriggers($hostId, $hostName);
    }
}
