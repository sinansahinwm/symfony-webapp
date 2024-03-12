<?php namespace App\Controller\Admin;

use App\Service\PuppeteerReplay\PuppeteerReplayService;
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
        $playEnvelope = $puppeteerReplayService->setOptions("dgsg", "sdgsdg")->play("/Users/meehouapp/Desktop/replay.js");
        return new JsonResponse([]);
    }

}