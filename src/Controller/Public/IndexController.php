<?php namespace App\Controller\Public;

use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route(path: '/', name: 'index')]
    public function index(): Response
    {
        return $this->redirectToRoute(LoginFormAuthenticator::SIGNIN_ROUTE);
    }

}