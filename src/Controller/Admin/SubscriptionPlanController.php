<?php

namespace App\Controller\Admin;

use App\Repository\SubscriptionPlanRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/subscription/plan', name: 'app_admin_subscription_plan_')]
class SubscriptionPlanController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(SubscriptionPlanRepository $subscriptionPlanRepository): Response
    {
        $thePlans = $subscriptionPlanRepository->findAll();
        return $this->render('admin/subscription_plan/index.html.twig', [
            'plans' => $thePlans,
        ]);
    }
}
