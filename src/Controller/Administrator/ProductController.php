<?php namespace App\Controller\Administrator;

use App\Controller\Admin\Table\ProductTable;
use App\Entity\Product;
use App\Service\CrudTable\CrudTableService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/administrator/product', name: 'app_administrator_product_')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request, CrudTableService $crudTableService): Response
    {
        $webScrapingRequestTable = $crudTableService->createFromFQCN($request, ProductTable::class);
        return $this->render('administrator/product/index.html.twig', [
            'productTable' => $webScrapingRequestTable,
        ]);
    }

    #[Route('/go_to_product/{theProduct}', name: 'go_to_product')]
    public function goToProduct(Product $theProduct): RedirectResponse
    {
        return $this->redirect($theProduct->getUrl());
    }

    #[Route('/go_to_product_marketplace/{theProduct}', name: 'go_to_product_marketplace')]
    public function goToProductMarketplace(Product $theProduct): RedirectResponse
    {
        return $this->redirect($theProduct->getMarketplace()->getUrl());
    }

}