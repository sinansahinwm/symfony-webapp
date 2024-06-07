<?php namespace App\Controller\Administrator;

use App\Controller\Admin\Table\WebScrapingRequestTable;
use App\Entity\WebScrapingRequest;
use App\Form\Administrator\WebScrapingRequestType;
use App\Repository\WebScrapingRequestRepository;
use App\Service\CrudTable\CrudTableService;
use App\Service\DomContentFramerService;
use App\Service\WebScrapingRequestService;
use App\Service\WebScrapingRequestStep\ChangeStep;
use App\Service\WebScrapingRequestStep\KeyDownStep;
use App\Service\WebScrapingRequestStep\KeyUpStep;
use Doctrine\ORM\EntityManagerInterface;
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
        $this->addRandomWebScrapingRequests($webScrapingRequestService);
        $this->addFlash('pageNotificationSuccess', t('Rastgele URL kuyruğa eklendi.'));
        return $this->redirectToRoute('app_administrator_web_scraping_request_index');
    }

    private function addRandomWebScrapingRequests(WebScrapingRequestService $webScrapingRequestService): void
    {
        $randomKeywords = ["kışlık mont", "atkı", "bere", "altın", "magsafe kılıf", "araç içi disko topu", "avize", "lamba", "185 65 15 araç lastiği", "michelin araba lastiği", "askılık", "abaküs", "şarap kadehi", "ruj", "samsung telefon kılıfı", "pensan mavi tükenmez kalem", "scricks dolma kalem", "dimes meyve suyu", "koroplast alüminyum folyo", "saat", "bilgisayar", "kablosuz klavye", "cam damacana", "elektrikli bisiklet", "prada gözlük", "mentos", "amd işlemci", "grissini", "protein tozu", "bahs"];
        $randomMarketplaces = [
            [
                "navigateURL" => "https://amazon.com.tr",
                'searchSelector' => "xpath///*[@id='twotabsearchtextbox']"
            ],
            [
                "navigateURL" => "https://www.trendyol.com",
                'searchSelector' => "xpath///*[@class='V8wbcUhU']"
            ],
            [
                "navigateURL" => "https://www.decathlon.com.tr",
                'searchSelector' => 'xpath///*[@data-anly="global-search-input"]'
            ],
            [
                "navigateURL" => "https://www.n11.com",
                'searchSelector' => 'xpath///*[@id="searchData"]'
            ],
            [
                "navigateURL" => "https://www.ciceksepeti.com",
                'searchSelector' => 'xpath///*[@data-actual-select-input="true"]'
            ],
            [
                "navigateURL" => "https://www.migros.com.tr",
                'searchSelector' => 'xpath///*[@id="product-search-combobox--trigger"]'
            ],
            [
                "navigateURL" => "https://www.pttavm.com",
                'searchSelector' => 'xpath///*[@placeholder="Arama yap"]'
            ],
            [
                "navigateURL" => "https://www.rossmann.com.tr",
                'searchSelector' => 'xpath///*[@id="search"]'
            ],
            [
                "navigateURL" => "https://www.koctas.com.tr",
                'searchSelector' => 'xpath///*[@type="search"]'
            ],
            [
                "navigateURL" => "https://www.lastikborsasi.com",
                'searchSelector' => 'xpath///*[@id="desktop-q"]'
            ],
            [
                "navigateURL" => "https://www.idefix.com",
                'searchSelector' => 'xpath///*[@id="headerSearch-d"]'
            ],
            [
                "navigateURL" => "https://www.kigili.com/",
                'searchSelector' => 'xpath///*[@name="q"]'
            ]

        ];

        foreach ($randomKeywords as $randomKeyword) {
            foreach ($randomMarketplaces as $randomMarketplace) {

                // Get Data
                $navigateURL = $randomMarketplace["navigateURL"];
                $searchSelector = $randomMarketplace["searchSelector"];

                $myStep1 = new ChangeStep($randomKeyword, [$searchSelector]);
                $myStep2 = new KeyDownStep("Enter");
                $myStep3 = new KeyUpStep("Enter");

                // Create Web Scraping Requests
                $webScrapingRequestService->clearSteps()
                    ->addStep($myStep1)
                    ->addStep($myStep2)
                    ->addStep($myStep3)
                    ->createRequest($navigateURL);

            }
        }
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
    public function showHtml(WebScrapingRequest $webScrapingRequest): Response
    {
        return $this->render('administrator/web_scraping_request/show_html.html.twig', [
            'webScrapingRequest' => $webScrapingRequest,
            'decodedContent' => base64_decode($webScrapingRequest->getConsumedContent()),
        ]);
    }

    #[Route('/content_iframe/{webScrapingRequest}', name: 'content_iframe')]
    public function contentIFrame(WebScrapingRequest $webScrapingRequest, DomContentFramerService $domContentFramerService): Response
    {
        $framedContent = $domContentFramerService->setHtml($webScrapingRequest->getConsumedContent())->setBaseURL($webScrapingRequest->getNavigateUrl());
        return new Response($framedContent->getFramedContent(FALSE, FALSE, TRUE), 200);
    }


    #[Route('/delete_all', name: 'delete_all')]
    public function deleteAll(WebScrapingRequestRepository $webScrapingRequestRepository, EntityManagerInterface $entityManager): Response
    {
        $webScrapingRequestRepository->deleteAll();
        $this->addFlash('pageNotificationSuccess', t("Tüm ögeler silindi"));
        return $this->redirectToRoute('app_administrator_web_scraping_request_index');
    }

}
