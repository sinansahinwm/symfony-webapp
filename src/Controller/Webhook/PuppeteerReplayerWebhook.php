<?php namespace App\Controller\Webhook;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/webhook', name: "webhook_")]
class PuppeteerReplayerWebhook extends BaseWebhook
{

    #[Route('/puppeteer_replayer', name: "puppeteer_replayer")]
    public function hook(): Response
    {
        exit("HANDLE WEB HOOK");
    }

    public function getAbsoluteUrl(): string
    {
        return $this->generateAbsoluteUrl("webhook_puppeteer_replayer");
    }

}