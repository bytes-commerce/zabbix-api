<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Subscriber;

use BytesCommerce\ZabbixApi\Contract\EntityExclusionProviderInterface;
use BytesCommerce\ZabbixApi\Contract\ZabbixNamingProviderInterface;
use BytesCommerce\ZabbixApi\Message\PushMetricMessage;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
#[AsDoctrineListener(event: Events::postRemove)]
final class DoctrineEntitySubscriber
{
    /** @var list<class-string> */
    private array $excludedEntityClasses;

    /**
     * @param iterable<EntityExclusionProviderInterface> $exclusionProviders
     */
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly ZabbixNamingProviderInterface $naming,
        #[Autowire('%kernel.environment%')]
        private readonly string $appEnv,
        #[AutowireIterator('zabbix.entity_exclusion_provider')]
        iterable $exclusionProviders = [],
    ) {
        $excluded = [];
        foreach ($exclusionProviders as $provider) {
            foreach ($provider->getExcludedEntityClasses() as $class) {
                $excluded[] = $class;
            }
        }
        $this->excludedEntityClasses = array_unique($excluded);
    }

    public function postPersist(PostPersistEventArgs $event): void
    {
        $this->dispatch($event->getObject(), 'entity.persist.success');
    }

    public function postUpdate(PostUpdateEventArgs $event): void
    {
        $this->dispatch($event->getObject(), 'entity.update.success');
    }

    public function postRemove(PostRemoveEventArgs $event): void
    {
        $this->dispatch($event->getObject(), 'entity.remove.success');
    }

    private function dispatch(object $entity, string $metricKey): void
    {
        if ($this->isExcluded($entity)) {
            return;
        }

        $entityClass = str_replace('\\', '.', $entity::class);

        $this->bus->dispatch(new PushMetricMessage(
            key: $this->naming->getItemKey($metricKey),
            value: 1,
            tags: [
                'env' => $this->appEnv,
                'entity' => $entityClass,
            ],
        ));
    }

    private function isExcluded(object $entity): bool
    {
        foreach ($this->excludedEntityClasses as $excludedClass) {
            if ($entity instanceof $excludedClass) {
                return true;
            }
        }

        return false;
    }
}
