<?php namespace App\Controller\Administrator;

use App\Controller\Admin\Table\MarketplaceTable;
use App\Entity\Marketplace;
use App\Form\Administrator\MarketplaceSearchKeywordType;
use App\Form\Administrator\MarketplaceType;
use App\Repository\ProductRepository;
use App\Service\CrudTable\CrudTableService;
use App\Service\MarketplaceSearchService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function Symfony\Component\Translation\t;

#[IsGranted('ROLE_ADMIN')]
#[Route('/administrator/marketplace', name: 'app_administrator_marketplace_')]
class MarketplaceController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, CrudTableService $crudTableService): Response
    {
        $webScrapingRequestTable = $crudTableService->createFromFQCN($request, MarketplaceTable::class);
        return $this->render('administrator/marketplace/index.html.twig', [
            'marketplaceTable' => $webScrapingRequestTable,
        ]);
    }

    #[Route('/redirecturl/{theMarketplace}', name: 'redirecturl')]
    public function redirectURL(Marketplace $theMarketplace): Response
    {
        return $this->redirect($theMarketplace->getUrl());
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $marketplace = new Marketplace();
        $form = $this->createForm(MarketplaceType::class, $marketplace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('pageNotificationSuccess', t("Pazaryeri başarıyla oluşturuldu."));
            $entityManager->persist($marketplace);
            $entityManager->flush();
            return $this->redirectToRoute('app_administrator_marketplace_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('administrator/marketplace/new.html.twig', [
            'marketplace' => $marketplace,
            'form' => $form,
        ]);
    }

    #[Route('/show/{theMarketplace}', name: 'show')]
    public function show(Marketplace $theMarketplace): Response
    {
        return $this->render('administrator/marketplace/show.html.twig', [
            'marketplace' => $theMarketplace,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(Request $request, Marketplace $marketplace, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MarketplaceType::class, $marketplace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('pageNotificationSuccess', t("Pazaryeri bilgileri başarıyla kaydedildi."));
            $entityManager->flush();
            return $this->redirectToRoute('app_administrator_marketplace_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('administrator/marketplace/edit.html.twig', [
            'marketplace' => $marketplace,
            'form' => $form,
        ]);
    }

    #[Route('/search_keyword', name: 'search_keyword')]
    public function searchKeyword(Request $request, MarketplaceSearchService $marketplaceSearchService): Response
    {
        $searchKeywordForm = $this->createForm(MarketplaceSearchKeywordType::class);
        $searchKeywordForm->handleRequest($request);

        if ($searchKeywordForm->isSubmitted() && $searchKeywordForm->isValid()) {

            // Get Unmapped Form Data
            $formKeyword = $searchKeywordForm->get("keyword")->getData();
            $formMarketplaces = $searchKeywordForm->get("marketplaces")->getData();

            // Proccess Search Keyword Form
            $this->proccessSearchKeywordForm($formKeyword, $formMarketplaces, $marketplaceSearchService);

            $this->addFlash('pageNotificationSuccess', t("Arama işlemi başlatıldı."));
            return $this->redirectToRoute('app_administrator_marketplace_search_keyword', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('administrator/marketplace/search_keyword.html.twig', [
            'form' => $searchKeywordForm
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Marketplace $marketplace, EntityManagerInterface $entityManager, ProductRepository $productRepository): Response
    {
        // Remove Products
        $myProducts = $productRepository->findBy(["marketplace" => $marketplace]);
        foreach ($myProducts as $myProduct) {
            $entityManager->remove($myProduct);
        }
        $entityManager->flush();

        // Remove Marketplace
        $entityManager->remove($marketplace);
        $entityManager->flush();

        // Release Message
        $this->addFlash('pageNotificationSuccess', t("Pazaryeri başarıyla silindi."));

        return $this->redirectToRoute('app_administrator_marketplace_index', [], Response::HTTP_SEE_OTHER);
    }

    private function proccessSearchKeywordForm(string $searchKeyword, iterable $marketplaces, MarketplaceSearchService $marketplaceSearchService): void
    {
        $explodedSearchKeywords = explode(PHP_EOL, $searchKeyword);
        foreach ($explodedSearchKeywords as $explodedSearchKeyword) {
            $trimmedSearchKeyword = trim($explodedSearchKeyword);
            if (strlen($trimmedSearchKeyword) > 1) {
                foreach ($marketplaces as $marketplace) {
                    if ($marketplace instanceof Marketplace) {
                        $marketplaceSearchService->searchKeyword($trimmedSearchKeyword, $marketplace);
                    }
                }
            }
        }

    }
}
