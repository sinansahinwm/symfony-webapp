<?php namespace App\EventListener;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEventListener(event: KernelEvents::REQUEST, method: "onKernelRequest")]
class KernelRequestListener
{
    public function __construct(private Security $security, private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function onKernelRequest(RequestEvent $requestEvent): void
    {

        // Handle Subscription Plan
        if (!str_contains($requestEvent->getRequest()->getPathInfo(), 'admin/exclude')) {
            $loggedUser = $this->security->getUser();
            if ($loggedUser !== NULL) {
                // Check : If User's Subscription Plan Is NULL
                if ($loggedUser->getSubscriptionPlan() === NULL) {
                    if (!in_array('ROLE_ADMIN', $loggedUser->getRoles())) {
                        $redirectURL = $this->urlGenerator->generate('app_admin_exclude_subscription_plan_index');
                        $requestEvent->setResponse(new RedirectResponse($redirectURL));
                    }
                }
            }
        }

    }

}