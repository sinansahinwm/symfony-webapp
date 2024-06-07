<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SearchKeywordType;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/admin/search', name: 'app_admin_search_')]
class SearchController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        $searchForm = $this->createForm(SearchKeywordType::class);
        $searchForm->handleRequest($request);
        $searchResults = [];
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            // TODO : Search
        }
        return $this->render('admin/search/index.html.twig', ['searchForm' => $searchForm, "searchResults" => $searchResults]);
    }


}
