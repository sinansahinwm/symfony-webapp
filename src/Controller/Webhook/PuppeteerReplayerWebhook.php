<?php namespace App\Controller\Webhook;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/webhook', name: "webhook_")]
class PuppeteerReplayerWebhook extends BaseWebhook implements WebHookInterface
{

    #[Route('/puppeteer_replayer', name: "puppeteer_replayer")]
    public function hook(Request $request): Response
    {
        if ($this->hookIsGranted($request) === TRUE) {
            $this->handleHook($request);
            return new Response(NULL, 200);
        } else {
            return new Response(NULL, 401);
        }
    }

    public function getAbsoluteUrl(): string
    {
        return $this->generateAbsoluteUrl("webhook_puppeteer_replayer");
    }

    private function handleHook(Request $request): void
    {
        exit("HANDLE HOOK");
    }

}