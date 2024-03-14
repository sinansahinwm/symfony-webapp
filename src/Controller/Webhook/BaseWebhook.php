<?php namespace App\Controller\Webhook;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

}