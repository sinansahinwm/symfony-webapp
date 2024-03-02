<?php namespace App\EventListener;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Translation\LocaleSwitcher;

#[AsEventListener(event: KernelEvents::REQUEST, priority: 20)]
class UserLocaleListener
{
    public function __construct(private LocaleSwitcher $localeSwitcher, private Security $security)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        // If User Is Logged, Look for User's Locale
        if ($loggedUser = $this->security->getUser()) {
            if ($usersLocale = $loggedUser->getLocale() !== NULL) {
                $this->localeSwitcher->setLocale($usersLocale);
            }
        }
    }
}