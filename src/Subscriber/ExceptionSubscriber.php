<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Subscriber;

use BytesCommerce\ZabbixApi\Contract\ExceptionExclusionProviderInterface;
use BytesCommerce\ZabbixApi\Contract\ZabbixNamingProviderInterface;
use BytesCommerce\ZabbixApi\Message\PushEventMessage;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

final class ExceptionSubscriber implements EventSubscriberInterface
{
    /** @var list<class-string<Throwable>> */
    private array $excludedExceptionClasses;

    /**
     * @param iterable<ExceptionExclusionProviderInterface> $exclusionProviders
     */
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly ZabbixNamingProviderInterface $naming,
        #[Autowire('%kernel.environment%')]
        private readonly string $appEnv,
        #[AutowireIterator('zabbix.exception_exclusion_provider')]
        iterable $exclusionProviders = [],
    ) {
        $excluded = [];
        foreach ($exclusionProviders as $provider) {
            foreach ($provider->getExcludedExceptionClasses() as $class) {
                $excluded[] = $class;
            }
        }
        $this->excludedExceptionClasses = array_unique($excluded);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onException', -100],
        ];
    }

    public function onException(ExceptionEvent $event): void
    {
        $req = $event->getRequest();
        $cid = (string) $req->attributes->get('_mon_cid', '');
        $route = (string) ($req->attributes->get('_route') ?? 'unknown');

        $exception = $event->getThrowable();

        if ($this->isExcluded($exception)) {
            return;
        }

        $this->bus->dispatch(new PushEventMessage(
            key: $this->naming->getItemKey('error.exception'),
            payload: [
                'class' => $exception::class,
                'message' => mb_substr($exception->getMessage(), 0, 500),
                'code' => $exception->getCode(),
                'route' => $route,
                'correlationId' => $cid,
                'env' => $this->appEnv,
            ],
            correlationId: $cid,
        ));
    }

    private function isExcluded(Throwable $exception): bool
    {
        foreach ($this->excludedExceptionClasses as $excludedClass) {
            if ($exception instanceof $excludedClass) {
                return true;
            }
        }

        return false;
    }
}
