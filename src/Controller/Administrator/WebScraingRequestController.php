<?php namespace App\Controller\Administrator;

use App\Controller\Admin\Table\SubscriptionPlanTable;
use App\Controller\Admin\Table\WebScrapingRequestTable;
use App\Entity\WebScrapingRequest;
use App\Service\CrudTable\CrudTableService;
use App\Service\WebScrapingRequestService;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Faker\Generator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function Symfony\Component\Translation\t;

#[IsGranted('ROLE_ADMIN')]
#[Route('/administrator/web/scraping/request', name: 'app_administrator_web_scraping_request_')]
class WebScraingRequestController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, CrudTableService $crudTableService): Response
    {
        $webScrapingRequestTable = $crudTableService->createFromFQCN($request, WebScrapingRequestTable::class);
        return $this->render('administrator/web_scraping_request/index.html.twig', [
            'webScrapingRequestTable' => $webScrapingRequestTable,
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(WebScrapingRequestService $webScrapingRequestService): Response
    {
        $randomInt = random_int(1000, 2000);
        $randomNavigateURL = "https://placehold.co/1000x$randomInt/jpg";
        $webScrapingRequestService->createRequest($randomNavigateURL);
        $this->addFlash('pageNotificationSuccess', t('Rastgele URL kuyruğa eklendi.'));
        return $this->redirectToRoute('app_administrator_web_scraping_request_index');
    }


    #[Route('/delete/{webScrapingRequest}', name: 'delete')]
    public function delete(WebScrapingRequest $webScrapingRequest, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($webScrapingRequest);
        $entityManager->flush();
        return $this->redirectToRoute('app_administrator_web_scraping_request_index');
    }

    #[Route('/show_html/{webScrapingRequest}', name: 'show_html')]
    public function showHtml(WebScrapingRequest $webScrapingRequest, EntityManagerInterface $entityManager): Response
    {
        return $this->render('administrator/web_scraping_request/show_html.html.twig', [
            'webScrapingRequest' => $webScrapingRequest,
            'decodedContent' => base64_decode($webScrapingRequest->getConsumedContent()),
        ]);
    }

    #[Route('/content_iframe/{webScrapingRequest}', name: 'content_iframe')]
    public function contentIFrame(WebScrapingRequest $webScrapingRequest, EntityManagerInterface $entityManager): Response
    {
        return new Response(base64_decode($webScrapingRequest->getConsumedContent()), 200);
    }

}
