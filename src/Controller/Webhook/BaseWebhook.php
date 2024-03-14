<?php namespace App\Controller\Webhook;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BaseWebhook extends AbstractController
{
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function generateAbsoluteUrl(string $routeName, array $routeParameters = []): string
    {
        return $this->urlGenerator->generate($routeName, $routeParameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    protected function hookIsGranted(Request $request): bool
    {
        $authHeaderName = $this->getParameter("app.cloud_functions.auth_header");
        $authHeaderValue = $this->getParameter("app.cloud_functions.auth_secret");
        $requestedHeaderVal = $request->headers->get($authHeaderName);
        return $requestedHeaderVal === $authHeaderValue;
    }

}