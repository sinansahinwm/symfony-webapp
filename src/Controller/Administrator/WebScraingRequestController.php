<?php namespace App\Controller\Administrator;

use App\Controller\Admin\Table\WebScrapingRequestTable;
use App\Entity\WebScrapingRequest;
use App\Form\Administrator\WebScrapingRequestType;
use App\Service\CrudTable\CrudTableService;
use App\Service\WebScrapingRequestService;
use App\Service\WebScrapingRequestStep\ChangeStep;
use App\Service\WebScrapingRequestStep\KeyDownStep;
use App\Service\WebScrapingRequestStep\KeyUpStep;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
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
        $myFaker = Factory::create("tr_TR");

        for ($x = 0; $x <= 5; $x++) {

            // Create Random Keyword
            $fakerRandomKeyword = $myFaker->city();

            // Add Steps
            $myStep1 = new ChangeStep($fakerRandomKeyword, ["xpath///*[@id='twotabsearchtextbox']"]);
            $myStep2 = new KeyDownStep("Enter");
            $myStep3 = new KeyUpStep("Enter");

            // Create Web Scraping Requests
            $webScrapingRequestService->clearSteps()
                ->addStep($myStep1)
                ->addStep($myStep2)
                ->addStep($myStep3)
                ->createRequest("https://amazon.com.tr");

        }


        $this->addFlash('pageNotificationSuccess', t('Rastgele URL kuyruğa eklendi.'));
        return $this->redirectToRoute('app_administrator_web_scraping_request_index');
    }

    #[Route('/new_by_url', name: 'new_by_url', methods: ['GET', 'POST'])]
    public function newByUrl(Request $request, WebScrapingRequestService $webScrapingRequestService): Response
    {
        $webScrapingRequest = new WebScrapingRequest();
        $form = $this->createForm(WebScrapingRequestType::class, $webScrapingRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('pageNotificationSuccess', t("URL kuyruğa eklendi."));
            $webScrapingRequestService->createRequest($webScrapingRequest->getNavigateUrl());
            return $this->redirectToRoute('app_administrator_web_scraping_request_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('administrator/web_scraping_request/new.html.twig', [
            'web_scraping_request' => $webScrapingRequest,
            'form' => $form,
        ]);
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
