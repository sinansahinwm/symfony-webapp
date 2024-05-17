<?php

namespace App\Controller\Admin;

use App\Entity\SubscriptionPlan;
use App\Entity\User;
use App\Form\PlanCheckoutType;
use App\Repository\SubscriptionPlanRepository;
use App\Security\LoginFormAuthenticator;
use App\Service\IyzicoPaymentService;
use App\Service\SubscriptionPlanCheckoutService;
use App\Service\UserSubscriptionService;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Iyzipay\Model\BasketItemType;
use Iyzipay\Model\Currency;
use Iyzipay\Model\Locale;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function subscribePlan(Request $request, SubscriptionPlan $thePlan, SubscriptionPlanCheckoutService $subscriptionPlanCheckoutService, #[CurrentUser] User $loggedUser, EntityManagerInterface $entityManager, UserSubscriptionService $userSubscriptionService): Response
    {

        // Select Plan & Start Trial Period
        if ($loggedUser->getSubscriptionPlan() === NULL) {
            if ($loggedUser->isTrialPeriodUsed() !== TRUE) {

                // Calculate Valid Until
                $validUntilDT = new DateTime();
                $trialPeriodDays = $thePlan->getTrialPeriodDays();
                $validUntilDT->modify('+' . $trialPeriodDays . " day");
                $trialPlanValidUntilAt = DateTimeImmutable::createFromMutable($validUntilDT);

                // Set Plan Details
                $loggedUser->setSubscriptionPlanValidUntil($trialPlanValidUntilAt);
                $loggedUser->setTrialPeriodUsed(TRUE);
                $loggedUser->setSubscriptionPlan($thePlan);

                // Persist Plan
                $entityManager->persist($loggedUser);
                $entityManager->flush();
                $this->addFlash('pageNotificationSuccess', $thePlan->getTrialPeriodDays() . t(" günlük deneme aboneliğiniz başladı."));
                return $this->redirectToRoute('app_admin_dashboard');
            }
        }

        // Handle Checout Form
        $checkoutForm = $this->createForm(PlanCheckoutType::class);
        $checkoutForm->handleRequest($request);

        if ($checkoutForm->isSubmitted() && $checkoutForm->isValid()) {

            $checkoutLocale = ($request->getLocale() === "tr") ? Locale::TR : Locale::EN;

            $myCheckout = $subscriptionPlanCheckoutService->checkoutWithForm($loggedUser, $thePlan, $checkoutForm, $checkoutLocale);
            $checkoutIsSuccess = $myCheckout->isPaymentSuccess();
            $lastCheckoutError = $myCheckout->getLastPaymentError();

            if ($checkoutIsSuccess === TRUE) {
                $userPaymentProof = $myCheckout->getUserPaymentProof();
                $userSubscriptionService->subscribeUserAfterPayment($loggedUser, $thePlan, $userPaymentProof);
                $this->addFlash('pageNotificationSuccess', t("Abonelik planı satın alındı."));
                return $this->redirectToRoute(LoginFormAuthenticator::REDIRECT_ROUTE_AFTER_SUBSCRIPTION_COMPLETED);
            } else {
                $this->addFlash("pageNotificationError", t("Ödeme başarısız.") . $lastCheckoutError);
            }

        }

        return $this->render('admin/subscription_plan/subscribe_plan.html.twig', [
            'thePlan' => $thePlan,
            'checkoutForm' => $checkoutForm
        ]);
    }
}
