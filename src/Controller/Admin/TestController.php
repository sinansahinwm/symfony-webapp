<?php namespace App\Controller\Admin;

use App\Controller\Webhook\PuppeteerReplayerWebhook;
use App\Repository\PuppeteerReplayHookRecordRepository;
use App\Service\DomContentFramerService;
use App\Service\PuppeteerReplayService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/test', name: 'app_admin_test_')]
class TestController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(DomContentFramerService $domContentFixerService, PuppeteerReplayHookRecordRepository $hookRecordRepository): Response
    {


        $theContent = $hookRecordRepository->find(659);
        $newHTML = $domContentFixerService
            ->setHtml($theContent->getContent())
            ->setUrlSchemeSource($theContent->getInitialPageUrl())
            ->extractData();
        exit(json_encode($newHTML));
        return new Response($newHTML);
    }

}