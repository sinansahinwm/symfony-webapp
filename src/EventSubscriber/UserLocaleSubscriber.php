<?php namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class UserLocaleSubscriber implements EventSubscriberInterface
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $eventUser = $event->getUser();
        if (null !== $eventUser->getLocale()) {
            $this->requestStack->getSession()->set('_locale', $eventUser->getLocale());
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
        ];
    }
}