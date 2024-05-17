<?php

namespace App\Controller\Admin;

use App\Entity\SubscriptionPlan;
use App\Entity\User;
use App\Form\PlanCheckoutType;
use App\Repository\SubscriptionPlanRepository;
use App\Service\IyzicoPaymentService;
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
    public function subscribePlan(Request $request, SubscriptionPlan $thePlan, #[CurrentUser] User $loggedUser, EntityManagerInterface $entityManager, IyzicoPaymentService $iyzicoPaymentService): Response
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

        // Handle Form
        $checkoutForm = $this->createForm(PlanCheckoutType::class);
        $checkoutForm->handleRequest($request);

        if ($checkoutForm->isSubmitted() && $checkoutForm->isValid()) {

            // --- Checkout Start --- //
            $iyzicoPayment = $iyzicoPaymentService
                ->preparePaymentRequest(Locale::TR, Currency::TL, 110, 500, "23235235", "sdgsdgsdg")
                ->setCreditCard("John Doe", "5528790000000008", "12", "2030", "123")
                ->setBuyerUser($loggedUser)
                ->setUserAddress($loggedUser)
                ->addBasketItem("1234235", "sdgsdgsdg", BasketItemType::VIRTUAL, 500)
                ->checkout();

            if ($iyzicoPayment !== NULL) {
                $paymentStatus = $iyzicoPayment->getStatus();
                $paymentErrorMessage = $iyzicoPayment->getErrorMessage();
                $paymentPhase = $iyzicoPayment->getPhase();
                exit(serialize($paymentStatus));
                exit("CHECK");
            }

            // --- Checkout End --- //

        }

        return $this->render('admin/subscription_plan/subscribe_plan.html.twig', [
            'thePlan' => $thePlan,
            'checkoutForm' => $checkoutForm
        ]);
    }
}
