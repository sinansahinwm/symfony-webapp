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
            ->setWebhookUrl("https://webhook-test.com/fd517bb52f8ee0aaf77803a02da4c597")
            ->setInstanceID("my instance id")
            ->play();

        //$myNodeApp = $puppeteerReplayerNodeApp->setRecordPath("/Users/meehouapp/Desktop/replay.js")->setOptions("https://DENEME.com", "my instance id");
        //$nodeAppPackageReleaseManager->releaseApp($myNodeApp);
        return new JsonResponse([]);
    }

}