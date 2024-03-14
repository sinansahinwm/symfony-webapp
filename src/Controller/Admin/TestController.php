<?php namespace App\Controller\Admin;

use App\Service\NodeApp\NodeAppPackageReleaseManager;
use App\Service\NodeApp\PuppeteerReplayerNodeApp;
use App\Service\PuppeteerReplayService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/test', name: 'app_admin_test_')]
class TestController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(PuppeteerReplayService $puppeteerReplayService): Response
    {
        $playEnvelope = $puppeteerReplayService
            ->setRecordPath("/Users/meehouapp/Desktop/hb.json")
            ->setWebhookUrl("https://webhook-test.com/8f5cdbaa6ebcf5d72faf4fe1dcb1e854")
            ->setInstanceID("my instance id")
            ->play();

        return new JsonResponse([]);
    }

}