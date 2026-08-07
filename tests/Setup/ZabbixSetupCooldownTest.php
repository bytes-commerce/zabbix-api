<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Tests\Setup;

use BytesCommerce\ZabbixApi\Contract\ItemDefinitionProviderInterface;
use BytesCommerce\ZabbixApi\Contract\ZabbixNamingProviderInterface;
use BytesCommerce\ZabbixApi\Setup\TriggerProvisioner;
use BytesCommerce\ZabbixApi\Setup\ZabbixSetup;
use BytesCommerce\ZabbixApi\ZabbixClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class ZabbixSetupCooldownTest extends TestCase
{
    private ZabbixClientInterface&MockObject $client;
    private ZabbixNamingProviderInterface&MockObject $naming;
    private ItemDefinitionProviderInterface&MockObject $registry;

    protected function setUp(): void
    {
        $this->client = $this->createMock(ZabbixClientInterface::class);
        $this->naming = $this->createMock(ZabbixNamingProviderInterface::class);
        $this->registry = $this->createMock(ItemDefinitionProviderInterface::class);
    }

    public function testEnsureFastSkipsWhenSetupDisabled(): void
    {
        $setup = $this->makeSetup(setupEnabled: false, cooldownSeconds: 300);

        $this->client->expects($this->never())->method('call');
        $this->registry->expects($this->never())->method('getHostId');

        $setup->ensureFast();
    }

    public function testEnsureFastReturnsEarlyWhenHostIdIsCached(): void
    {
        $setup = $this->makeSetup(setupEnabled: true, cooldownSeconds: 300);

        $this->registry->expects($this->once())->method('getHostId')->willReturn('12345');
        $this->client->expects($this->never())->method('call');

        $setup->ensureFast();
    }

    public function testEnsureFastCatchesFailureAndEntersCooldown(): void
    {
        $setup = $this->makeSetup(setupEnabled: true, cooldownSeconds: 300);

        $this->registry->expects($this->exactly(2))->method('getHostId')->willReturn(null);
        $this->client->expects($this->once())
            ->method('call')
            ->willThrowException(new \RuntimeException('Zabbix unreachable'));

        $setup->ensureFast();
        $setup->ensureFast();
    }

    public function testClearFailureStateAllowsImmediateRetry(): void
    {
        $setup = $this->makeSetup(setupEnabled: true, cooldownSeconds: 300);

        $callCount = 0;
        $this->registry->method('getHostId')->willReturnCallback(function () use (&$callCount) {
            $callCount++;
            return null;
        });
        $this->client->method('call')->willThrowException(new \RuntimeException('Zabbix unreachable'));

        $setup->ensureFast();
        $setup->clearFailureState();
        $setup->ensureFast();

        $this->assertSame(2, $callCount, 'ensureFast should skip registry lookup while in cooldown');
    }

    private function makeSetup(bool $setupEnabled, int $cooldownSeconds): ZabbixSetup
    {
        return new ZabbixSetup(
            client: $this->client,
            naming: $this->naming,
            registry: $this->registry,
            logger: new NullLogger(),
            setupEnabled: $setupEnabled,
            setupFailureCooldownSeconds: $cooldownSeconds,
            triggerProvisioner: null,
        );
    }
}
