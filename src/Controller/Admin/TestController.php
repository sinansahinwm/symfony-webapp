<?php namespace App\Controller\Admin;

use App\Controller\Webhook\PuppeteerReplayerWebhook;
use App\Service\PuppeteerReplayService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/test', name: 'app_admin_test_')]
class TestController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(PuppeteerReplayService $puppeteerReplayService, PuppeteerReplayerWebhook $puppeteerReplayerWebhook): Response
    {
        $playEnvelope = $puppeteerReplayService
            ->setRecordPath("/Users/meehouapp/Desktop/hb.json")
            ->setWebhook($puppeteerReplayerWebhook)
            // ->setWebhookUrl("https://webhook-test.com/e11cca3022e60d860ee5671e3288f284")
            ->setInstanceID("my instance id")
            ->play();

        return new JsonResponse([]);
    }

}