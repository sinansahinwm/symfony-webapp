<?php namespace App\Controller\Webhook;

use App\Service\PuppeteerWebhookHandlerService;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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
        } else {
            throw new BadRequestHttpException();
        }
    }

    private function getHookContentAsArray(Request $request): array
    {
        $requestContent = $request->getContent();
        if (json_validate($requestContent) === TRUE) {
            return json_decode($requestContent, true, 512, JSON_OBJECT_AS_ARRAY);
        } else {
            throw new BadRequestHttpException();
        }
    }

}