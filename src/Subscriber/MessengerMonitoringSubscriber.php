<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Subscriber;

use BytesCommerce\ZabbixApi\Contract\MonitoringMessageInterface;
use BytesCommerce\ZabbixApi\Contract\ZabbixNamingProviderInterface;
use BytesCommerce\ZabbixApi\Message\PushMetricMessage;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class MessengerMonitoringSubscriber implements EventSubscriberInterface
{
    private const string ZABBIX_MESSAGE_NAMESPACE = 'BytesCommerce\\ZabbixApi\\Message\\';

    public function __construct(
        private MessageBusInterface $bus,
        private ZabbixNamingProviderInterface $naming,
        #[Autowire('%kernel.environment%')]
        private string $appEnv,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageReceivedEvent::class => 'onMessageReceived',
            WorkerMessageHandledEvent::class => 'onMessageHandled',
            WorkerMessageFailedEvent::class => 'onMessageFailed',
        ];
    }

    public function onMessageReceived(WorkerMessageReceivedEvent $event): void
    {
        if ($this->isMonitoringMessage($event)) {
            return;
        }

        $message = $event->getEnvelope()->getMessage();
        while ($message instanceof Envelope) {
            $message = $message->getMessage();
        }

        $messageClass = $this->getShortClassName($message::class);

        $this->bus->dispatch(new PushMetricMessage(
            key: $this->naming->getItemKey('messenger.received'),
            value: 1,
            tags: [
                'env' => $this->appEnv,
                'message_class' => $messageClass,
                'transport' => $event->getReceiverName(),
            ],
        ));
    }

    public function onMessageHandled(WorkerMessageHandledEvent $event): void
    {
        if ($this->isMonitoringMessage($event)) {
            return;
        }

        $message = $event->getEnvelope()->getMessage();
        while ($message instanceof Envelope) {
            $message = $message->getMessage();
        }

        $messageClass = $this->getShortClassName($message::class);

        $this->bus->dispatch(new PushMetricMessage(
            key: $this->naming->getItemKey('messenger.handled'),
            value: 1,
            tags: [
                'env' => $this->appEnv,
                'message_class' => $messageClass,
            ],
        ));
    }

    public function onMessageFailed(WorkerMessageFailedEvent $event): void
    {
        if ($this->isMonitoringMessage($event)) {
            return;
        }

        $message = $event->getEnvelope()->getMessage();
        while ($message instanceof Envelope) {
            $message = $message->getMessage();
        }

        $messageClass = $this->getShortClassName($message::class);
        $errorClass = $this->getShortClassName($event->getThrowable()::class);

        $this->bus->dispatch(new PushMetricMessage(
            key: $this->naming->getItemKey('messenger.failed.count'),
            value: 1,
            tags: [
                'env' => $this->appEnv,
                'message_class' => $messageClass,
                'error_class' => $errorClass,
            ],
        ));
    }

    private function isMonitoringMessage(WorkerMessageReceivedEvent|WorkerMessageHandledEvent|WorkerMessageFailedEvent $event): bool
    {
        $message = $event->getEnvelope()->getMessage();

        while ($message instanceof Envelope) {
            $message = $message->getMessage();
        }

        if ($message instanceof MonitoringMessageInterface) {
            return true;
        }

        return str_starts_with($message::class, self::ZABBIX_MESSAGE_NAMESPACE);
    }

    private function getShortClassName(string $fqcn): string
    {
        $parts = explode('\\', $fqcn);

        return end($parts);
    }
}
