<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'app_admin_')]
class DocumentationController extends AbstractController
{
    #[Route('/documentation', name: 'documentation')]
    public function index(): Response
    {
        return $this->render('admin/documentation/index.html.twig', [
            'controller_name' => 'DocumentationController',
        ]);
    }
}
