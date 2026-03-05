<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Subscriber;

use BytesCommerce\ZabbixApi\Contract\RouteExclusionProviderInterface;
use BytesCommerce\ZabbixApi\Contract\ZabbixNamingProviderInterface;
use BytesCommerce\ZabbixApi\Message\PushMetricMessage;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

final class RequestTransactionSubscriber implements EventSubscriberInterface
{
    /** @var array<string, true> */
    private array $excludedRoutes;

    /**
     * @param iterable<RouteExclusionProviderInterface> $exclusionProviders
     */
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly ZabbixNamingProviderInterface $naming,
        #[Autowire('%kernel.environment%')]
        private readonly string $appEnv,
        #[AutowireIterator('zabbix.route_exclusion_provider')]
        iterable $exclusionProviders = [],
    ) {
        $excluded = [];
        foreach ($exclusionProviders as $provider) {
            foreach ($provider->getExcludedRoutes() as $route) {
                $excluded[$route] = true;
            }
        }
        $this->excludedRoutes = $excluded;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onRequest', 10_000],
            KernelEvents::TERMINATE => ['onTerminate', -10_000],
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $req = $event->getRequest();
        $req->attributes->set('_mon_start', hrtime(true));
        $req->attributes->set('_mon_cid', Uuid::v7()->toRfc4122());
    }

    public function onTerminate(TerminateEvent $event): void
    {
        $req = $event->getRequest();
        $start = $req->attributes->get('_mon_start');
        if (!\is_int($start)) {
            return;
        }

        $cidRaw = $req->attributes->get('_mon_cid', '');
        $cid = is_string($cidRaw) ? $cidRaw : '';
        $durationMs = (hrtime(true) - $start) / 1_000_000;

        $routeRaw = $req->attributes->get('_route');
        $route = is_string($routeRaw) ? $routeRaw : 'unknown';
        if (isset($this->excludedRoutes[$route])) {
            return;
        }

        $status = $event->getResponse()->getStatusCode();
        $this->bus->dispatch(new PushMetricMessage(
            key: $this->naming->getItemKey('tx.duration_ms'),
            value: (float) $durationMs,
            tags: ['env' => $this->appEnv, 'route' => $route, 'method' => $req->getMethod(), 'status' => $status],
            correlationId: $cid,
        ));

        $this->bus->dispatch(new PushMetricMessage(
            key: $this->naming->getItemKey('tx.http_status'),
            value: (int) $status,
            tags: ['env' => $this->appEnv, 'route' => $route, 'method' => $req->getMethod()],
            correlationId: $cid,
        ));
    }
}
