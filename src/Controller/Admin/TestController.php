<?php namespace App\Controller\Admin;

use App\Service\NodeApp\PuppeteerReplayerNodeApp;
use App\Service\PuppeteerReplay\PuppeteerReplayService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/admin/test', name: 'app_admin_test_')]
class TestController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(PuppeteerReplayerNodeApp $puppeteerReplayerNodeApp, PuppeteerReplayService $puppeteerReplayService, HttpClientInterface $httpClient): Response
    {

        $puppeteerReplayerNodeApp->setRecordPath("/Users/meehouapp/Desktop/replay.js")->setOptions("https://DENEME.com", "my instance id")->releaseApp();
        exit("AppReleased");
        // $myReq = $httpClient->request('GET',"http://127.0.0.1:3030");
        // echo $myReq->getContent();
        // exit();
        $playEnvelope = $puppeteerReplayService->setOptions("dgsg", "sdgsdg")->play("/Users/meehouapp/Desktop/replay.js");
        return new JsonResponse([]);
    }

}