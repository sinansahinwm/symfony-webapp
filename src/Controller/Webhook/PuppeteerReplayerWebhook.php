<?php namespace App\Controller\Webhook;

use App\Service\PuppeteerWebhookHandlerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/webhook', name: "webhook_")]
class PuppeteerReplayerWebhook extends BaseWebhook implements WebHookInterface
{

    #[Route('/puppeteer_replayer', name: "puppeteer_replayer")]
    public function hook(Request $request, PuppeteerWebhookHandlerService $puppeteerWebhookHandlerService): Response
    {
        if ($this->hookIsGranted($request) === TRUE) {
            $this->handleHook($request, $puppeteerWebhookHandlerService);
            return new Response(NULL, 200);
        } else {
            return new Response(NULL, 401);
        }
    }

    public function getAbsoluteUrl(): string
    {
        return $this->generateAbsoluteUrl("webhook_puppeteer_replayer");
    }

    private function handleHook(Request $request, PuppeteerWebhookHandlerService $puppeteerWebhookHandlerService): void
    {
        $hookContent = $this->getHookContentAsArray($request);
        if ($hookContent) {
            $puppeteerWebhookHandlerService->handleHook($hookContent);
        }
    }

    private function getHookContentAsArray(Request $request): array|null
    {
        if (json_validate($request->getContent())) {
            return json_decode($request->getContent(), true, JSON_OBJECT_AS_ARRAY);
        }
        return NULL;
    }

}