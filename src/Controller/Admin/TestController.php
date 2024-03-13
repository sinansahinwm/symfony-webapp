<?php namespace App\Controller\Admin;

use App\Service\NodeApp\NodeAppPackageReleaseManager;
use App\Service\NodeApp\PuppeteerReplayerNodeApp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/test', name: 'app_admin_test_')]
class TestController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(PuppeteerReplayerNodeApp $puppeteerReplayerNodeApp, NodeAppPackageReleaseManager $nodeAppPackageReleaseManager): Response
    {
        $myNodeApp = $puppeteerReplayerNodeApp->setRecordPath("/Users/meehouapp/Desktop/replay.js")->setOptions("https://DENEME.com", "my instance id");
        $nodeAppPackageReleaseManager->releaseApp($myNodeApp);
        return new JsonResponse([]);
    }

}