<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Subscriber;

use BytesCommerce\ZabbixApi\Contract\ZabbixNamingProviderInterface;
use BytesCommerce\ZabbixApi\Message\PushEventMessage;
use BytesCommerce\ZabbixApi\Message\PushMetricMessage;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

final readonly class SecurityAuthSubscriber implements EventSubscriberInterface
{
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
            LoginSuccessEvent::class => 'onSuccess',
            LoginFailureEvent::class => 'onFailure',
        ];
    }

    public function onSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        $userId = $this->extractUserId($user);

        $this->bus->dispatch(new PushMetricMessage(
            key: $this->naming->getItemKey('auth.login.success'),
            value: 1,
            tags: ['env' => $this->appEnv],
        ));

        $this->bus->dispatch(new PushEventMessage(
            key: $this->naming->getItemKey('auth.login.success_event'),
            payload: ['userId' => $userId],
        ));
    }

    public function onFailure(LoginFailureEvent $event): void
    {
        $ex = $event->getException();
        $userIdentifier = $this->extractUserIdentifier($ex, $event->getPassport()?->getUser());

        $this->bus->dispatch(new PushMetricMessage(
            key: $this->naming->getItemKey('auth.login.failure'),
            value: 1,
            tags: ['env' => $this->appEnv],
        ));

        $this->bus->dispatch(new PushEventMessage(
            key: $this->naming->getItemKey('auth.login.failure_event'),
            payload: [
                'userIdentifier' => $userIdentifier,
                'exception' => $ex::class,
                'message' => mb_substr($ex->getMessage(), 0, 300),
            ],
        ));
    }

    private function extractUserId(mixed $user): string
    {
        if (!\is_object($user)) {
            return is_string($user) ? $user : 'unknown';
        }

        if (method_exists($user, 'getId')) {
            $id = $user->getId();
            if (\is_scalar($id)) {
                return (string) $id;
            }
        }

        if (method_exists($user, 'getUserIdentifier')) {
            $identifier = $user->getUserIdentifier();
            return is_string($identifier) ? $identifier : 'unknown';
        }

        return 'unknown';
    }

    private function extractUserIdentifier(AuthenticationException $exception, mixed $user): string
    {
        if (\is_object($user)) {
            if (method_exists($user, 'getUserIdentifier')) {
                $identifier = $user->getUserIdentifier();
                return is_string($identifier) ? $identifier : 'unknown';
            }

            if (method_exists($user, 'getId')) {
                $id = $user->getId();
                if (\is_scalar($id)) {
                    return (string) $id;
                }
            }

            return 'unknown';
        }

        if (\is_string($user)) {
            return $user;
        }

        $token = $exception->getToken();
        if ($token instanceof TokenInterface) {
            return $token->getUserIdentifier();
        }

        return 'unknown';
    }
}
