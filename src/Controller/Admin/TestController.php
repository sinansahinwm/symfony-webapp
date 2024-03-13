<?php namespace App\Controller\Admin;

use App\Service\PuppeteerReplay\PuppeteerReplayService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/admin/test', name: 'app_admin_test_')]
class TestController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(PuppeteerReplayService $puppeteerReplayService, HttpClientInterface $httpClient): Response
    {


        $myReq = $httpClient->request("http://127.0.0.1:3030");
        echo $myReq->getContent();
        exit();
        // $playEnvelope = $puppeteerReplayService->setOptions("dgsg", "sdgsdg")->play("/Users/meehouapp/Desktop/replay.js");
        return new JsonResponse([]);
    }

}