<?php namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin', name: 'app_admin_')]
class DashboardController extends AbstractController
{
    #[Route(path: '/dashboard', name: 'dashboard')]
    public function index(): Response
    {
        return $this->render('admin/documentation/index.html.twig');
    }
}