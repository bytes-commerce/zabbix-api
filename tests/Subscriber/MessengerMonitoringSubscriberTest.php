<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Tests\Subscriber;

use BytesCommerce\ZabbixApi\Contract\MonitoringMessageInterface;
use BytesCommerce\ZabbixApi\Contract\ZabbixNamingProviderInterface;
use BytesCommerce\ZabbixApi\Message\EnsureZabbixSetupMessage;
use BytesCommerce\ZabbixApi\Message\PushEventMessage;
use BytesCommerce\ZabbixApi\Message\PushMetricMessage;
use BytesCommerce\ZabbixApi\Subscriber\MessengerMonitoringSubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;

final class MessengerMonitoringSubscriberTest extends TestCase
{
    private MessageBusInterface&MockObject $bus;

    private ZabbixNamingProviderInterface&MockObject $naming;

    private MessengerMonitoringSubscriber $subscriber;

    protected function setUp(): void
    {
        $this->bus = $this->createMock(MessageBusInterface::class);
        $this->naming = $this->createMock(ZabbixNamingProviderInterface::class);
        $this->naming->method('getItemKey')->willReturnCallback(static fn (string $key) => 'app.' . $key);

        $this->subscriber = new MessengerMonitoringSubscriber(
            $this->bus,
            $this->naming,
            'test',
        );
    }

    public function testPushMetricMessageImplementsMonitoringMessageInterface(): void
    {
        $message = new PushMetricMessage('test.key', 1);
        $this->assertInstanceOf(MonitoringMessageInterface::class, $message);
    }

    public function testPushEventMessageImplementsMonitoringMessageInterface(): void
    {
        $message = new PushEventMessage('test.key', ['foo' => 'bar']);
        $this->assertInstanceOf(MonitoringMessageInterface::class, $message);
    }

    public function testOnMessageReceivedDoesNotDispatchForPushMetricMessage(): void
    {
        $monitoringMessage = new PushMetricMessage('test.key', 42);
        $envelope = new Envelope($monitoringMessage, [new ReceivedStamp('async')]);
        $event = new WorkerMessageReceivedEvent($envelope, 'async');

        $this->bus->expects($this->never())->method('dispatch');

        $this->subscriber->onMessageReceived($event);
    }

    public function testOnMessageHandledDoesNotDispatchForPushMetricMessage(): void
    {
        $monitoringMessage = new PushMetricMessage('test.key', 42);
        $envelope = new Envelope($monitoringMessage);
        $event = new WorkerMessageHandledEvent($envelope, 'async');

        $this->bus->expects($this->never())->method('dispatch');

        $this->subscriber->onMessageHandled($event);
    }

    public function testOnMessageFailedDoesNotDispatchForPushMetricMessage(): void
    {
        $monitoringMessage = new PushMetricMessage('test.key', 42);
        $envelope = new Envelope($monitoringMessage);
        $event = new WorkerMessageFailedEvent($envelope, 'async', new \RuntimeException('Test error'));

        $this->bus->expects($this->never())->method('dispatch');

        $this->subscriber->onMessageFailed($event);
    }

    public function testOnMessageReceivedDoesNotDispatchForPushEventMessage(): void
    {
        $monitoringMessage = new PushEventMessage('test.key', ['data' => 'value']);
        $envelope = new Envelope($monitoringMessage, [new ReceivedStamp('async')]);
        $event = new WorkerMessageReceivedEvent($envelope, 'async');

        $this->bus->expects($this->never())->method('dispatch');

        $this->subscriber->onMessageReceived($event);
    }

    public function testOnMessageHandledDoesNotDispatchForPushEventMessage(): void
    {
        $monitoringMessage = new PushEventMessage('test.key', ['data' => 'value']);
        $envelope = new Envelope($monitoringMessage);
        $event = new WorkerMessageHandledEvent($envelope, 'async');

        $this->bus->expects($this->never())->method('dispatch');

        $this->subscriber->onMessageHandled($event);
    }

    public function testOnMessageFailedDoesNotDispatchForPushEventMessage(): void
    {
        $monitoringMessage = new PushEventMessage('test.key', ['data' => 'value']);
        $envelope = new Envelope($monitoringMessage);
        $event = new WorkerMessageFailedEvent($envelope, 'async', new \RuntimeException('Test error'));

        $this->bus->expects($this->never())->method('dispatch');

        $this->subscriber->onMessageFailed($event);
    }

    public function testNoRecursiveMessageLoopOccursForPushMetricMessage(): void
    {
        $monitoringMessage = new PushMetricMessage('test.metric', 100);
        $envelope = new Envelope($monitoringMessage);

        $this->bus->expects($this->never())->method('dispatch');

        $this->subscriber->onMessageReceived(new WorkerMessageReceivedEvent($envelope, 'async'));
        $this->subscriber->onMessageHandled(new WorkerMessageHandledEvent($envelope, 'async'));
        $this->subscriber->onMessageFailed(new WorkerMessageFailedEvent($envelope, 'async', new \RuntimeException()));
    }

    public function testNoRecursiveMessageLoopOccursForPushEventMessage(): void
    {
        $monitoringMessage = new PushEventMessage('test.event', ['data' => 'value']);
        $envelope = new Envelope($monitoringMessage);

        $this->bus->expects($this->never())->method('dispatch');

        $this->subscriber->onMessageReceived(new WorkerMessageReceivedEvent($envelope, 'async'));
        $this->subscriber->onMessageHandled(new WorkerMessageHandledEvent($envelope, 'async'));
        $this->subscriber->onMessageFailed(new WorkerMessageFailedEvent($envelope, 'async', new \RuntimeException()));
    }

    public function testNoRecursiveMessageLoopOccursForEnsureZabbixSetupMessage(): void
    {
        $monitoringMessage = new EnsureZabbixSetupMessage();
        $envelope = new Envelope($monitoringMessage);

        $this->bus->expects($this->never())->method('dispatch');

        $this->subscriber->onMessageReceived(new WorkerMessageReceivedEvent($envelope, 'async'));
        $this->subscriber->onMessageHandled(new WorkerMessageHandledEvent($envelope, 'async'));
        $this->subscriber->onMessageFailed(new WorkerMessageFailedEvent($envelope, 'async', new \RuntimeException()));
    }

    public function testNoRecursiveMessageLoopOccursForNestedEnvelope(): void
    {
        $monitoringMessage = new PushMetricMessage('test.metric', 100);
        $envelope = new Envelope(new Envelope($monitoringMessage));

        $this->bus->expects($this->never())->method('dispatch');

        $this->subscriber->onMessageReceived(new WorkerMessageReceivedEvent($envelope, 'async'));
        $this->subscriber->onMessageHandled(new WorkerMessageHandledEvent($envelope, 'async'));
        $this->subscriber->onMessageFailed(new WorkerMessageFailedEvent($envelope, 'async', new \RuntimeException()));
    }
}
