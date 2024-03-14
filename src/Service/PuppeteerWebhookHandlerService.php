<?php namespace App\Service;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PuppeteerWebhookHandlerService
{
    public function handleHook(array $hookBodyData): void
    {
        throw new NotFoundHttpException();
    }
}