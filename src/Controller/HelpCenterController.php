<?php

namespace App\Controller;

use App\Repository\HelpCenterCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/help_center', name: 'app_admin_help_center_')]
class HelpCenterController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(HelpCenterCategoryRepository $helpCenterCategoryRepository): Response
    {
        $helpCenterCategories = $helpCenterCategoryRepository->findAll();

        return $this->render('admin/help_center/index.html.twig', [
            'helpCenterCategories' => $helpCenterCategories
        ]);
    }
}
