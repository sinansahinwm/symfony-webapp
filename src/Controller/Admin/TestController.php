<?php namespace App\Controller\Admin;

use App\Service\WebScrapingRequestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/test', name: 'app_admin_test_')]
class TestController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(WebScrapingRequestService $webScrapingRequestService): Response
    {
        foreach (range(100, 1000, 100) as $index => $item) {
            $webScrapingRequestService->createRequest('https://placehold.co/100x' . $index . '/png');
        }

        exit("s");
    }

}