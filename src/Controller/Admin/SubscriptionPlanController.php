<?php

namespace App\Controller\Admin;

use App\Entity\SubscriptionPlan;
use App\Entity\User;
use App\Repository\SubscriptionPlanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use function Symfony\Component\Translation\t;

#[Route('/admin/exclude/subscription/plan', name: 'app_admin_exclude_subscription_plan_')]
class SubscriptionPlanController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(SubscriptionPlanRepository $subscriptionPlanRepository): Response
    {
        $thePlans = $subscriptionPlanRepository->getAllSubscriotionPlansByOrder();
        return $this->render('admin/subscription_plan/index.html.twig', [
            'plans' => $thePlans,
        ]);
    }

    #[Route('/subscribe/{thePlan}', name: 'subscribe_plan')]
    public function subscribePlan(SubscriptionPlan $thePlan, #[CurrentUser] User $loggedUser, EntityManagerInterface $entityManager): Response
    {

        // Select Plan & Start Trial Period
        if ($loggedUser->getSubscriptionPlan() === NULL) {
            if ($loggedUser->isTrialPeriodUsed() !== TRUE) {
                $loggedUser->setTrialPeriodUsed(TRUE);
                $loggedUser->setSubscriptionPlan($thePlan);
                $entityManager->persist($loggedUser);
                $entityManager->flush();
                $this->addFlash('pageNotificationSuccess', $thePlan->getTrialPeriodDays() . t(" günlük deneme aboneliğiniz başladı."));
                return $this->redirectToRoute('app_admin_dashboard');
            }
        }

        return $this->render('admin/subscription_plan/subscribe_plan.html.twig', [
            'plan' => $thePlan
        ]);
    }
}
