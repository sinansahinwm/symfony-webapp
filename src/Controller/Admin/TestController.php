<?php namespace App\Controller\Admin;

use App\Service\PuppeteerReplay\PuppeteerReplayPackager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/test', name: 'app_admin_test_')]
class TestController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(PuppeteerReplayPackager $puppeteerReplayService): Response
    {
        $packagePath = $puppeteerReplayService->loadRecord("/Users/meehouapp/Desktop/replay.js")->package("sdg", "sdg");
        return new JsonResponse([]);
    }

}