<?php namespace App\Controller\Webhook;

use App\Service\PuppeteerWebhookHandlerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use function Symfony\Component\Translation\t;

#[Route('/webhook', name: "webhook_")]
class PuppeteerReplayerWebhook extends BaseWebhook implements WebHookInterface
{

    #[Route('/puppeteer_replayer', name: "puppeteer_replayer")]
    public function hook(Request $request, PuppeteerWebhookHandlerService $puppeteerWebhookHandlerService): Response
    {
        if ($this->hookIsGranted($request) === TRUE) {
            $puppeteerWebhookHandlerService->handleHook($this->getHookContentAsArray($request));
            return new Response(NULL, 200);
        } else {
            return new Response(NULL, 401);
        }
    }

    public function getAbsoluteUrl(): string
    {
        return $this->generateAbsoluteUrl("webhook_puppeteer_replayer");
    }

    private function getHookContentAsArray(Request $request): array
    {
        $requestContent = $request->getContent();
        if (json_validate($requestContent) === TRUE) {
            return json_decode($requestContent, true, 512, JSON_OBJECT_AS_ARRAY);
        } else {
            throw new BadRequestHttpException(t("Hatalı istek, içerik validasyonu başarısız. İçerik: ") . $requestContent);
        }
    }

}