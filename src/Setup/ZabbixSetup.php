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
    }

    public function ensureHost(): string
    {
        $hostName = $this->naming->getHostName();
        $cleanName = $this->naming->getCleanHostName();

        $result = $this->client->call(ZabbixAction::HOST_GET, [
            'filter' => ['name' => $hostName, 'host' => $cleanName],
            'output' => ['hostid'],
        ]);

        if (\count($result) > 0 && !empty($result[0]['hostid'])) {
            return $result[0]['hostid'];
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

        $hostId = $result['hostids'][0];
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

        if (\count($result) > 0 && !empty($result[0]['groupid'])) {
            return $result[0]['groupid'];
        }

        $result = $this->client->call(ZabbixAction::HOSTGROUP_CREATE, [
            'name' => $hostGroup,
        ]);

        Assert::keyExists($result, 'groupids', 'Failed to create Zabbix host group');
        $groupId = $result['groupids'][0];
        $this->logger->info('Zabbix host group created', ['group' => $hostGroup, 'groupid' => $groupId]);

        return $groupId;
    }

    private function ensureItems(string $hostId): void
    {
        $itemDefinitions = $this->registry->getAllItemDefinitions();

        foreach ($itemDefinitions as $suffix => $definition) {
            $key = $this->registry->getFullItemKey($suffix);

            $result = $this->client->call(ZabbixAction::ITEM_GET, [
                'hostids' => $hostId,
                'filter' => ['key_' => $key],
                'output' => ['itemid'],
            ]);

            if (\count($result) > 0 && !empty($result[0]['itemid'])) {
                $itemId = $result[0]['itemid'];
                $this->registry->setItemId($key, $itemId);

                continue;
            }

            try {
                $result = $this->client->call(ZabbixAction::ITEM_CREATE, [
                    'name' => $definition['name'],
                    'key_' => $key,
                    'hostid' => $hostId,
                    'type' => $definition['type'],
                    'value_type' => $definition['value_type'],
                    'history' => $definition['history'],
                ]);
            } catch (ZabbixApiException $e) {
                $this->logger->error('Failed to create Zabbix item', [
                    'key' => $key,
                    'error' => $e->getMessage(),
                    'errorData' => $e->getErrorData(),
                ]);

                continue;
            }

            Assert::keyExists($result, 'itemids', \sprintf('Failed to create Zabbix item, expected key "itemids" in response, got %s', implode(',', array_keys($result))));
            $itemId = $result['itemids'][0];
            $this->registry->setItemId($key, $itemId);
            $this->logger->info('Zabbix item created', ['key' => $key, 'itemid' => $itemId]);
        }
    }
}
