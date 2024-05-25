<?php namespace App\Controller\Administrator;

use App\Controller\Admin\Table\SubscriptionPlanTable;
use App\Entity\SubscriptionPlan;
use App\Form\Administrator\SubscriptionPlanType;
use App\Repository\UserRepository;
use App\Service\CrudTable\CrudTableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function Symfony\Component\Translation\t;

#[IsGranted('ROLE_ADMIN')]
#[Route('/administrator/subscription/plan', name: 'app_administrator_subscription_plan_')]
class SubscriptionPlanController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, CrudTableService $crudTableService): Response
    {
        $subscriptionPlanTable = $crudTableService->createFromFQCN($request, SubscriptionPlanTable::class);
        return $this->render('administrator/subscription_plan/index.html.twig', [
            'subscriptionPlanTable' => $subscriptionPlanTable,
        ]);
    }

    #[Route('/delete/{subscriptionPlan}', name: 'delete')]
    public function delete(SubscriptionPlan $subscriptionPlan, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $planUserExist = $userRepository->findOneBy(["subscription_plan" => $subscriptionPlan]);
        if ($planUserExist === NULL) {
            $entityManager->remove($subscriptionPlan);
            $entityManager->flush();
            $this->addFlash('pageNotificationSuccess', t("Abonelik planı başarıyla silindi."));
        } else {
            $this->addFlash('pageNotificationError', t("Bu abonelik planını halen kullanan kullanıcılar olduğu için plan silinemiyor."));
        }

        return $this->redirectToRoute('app_administrator_subscription_plan_index');
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $subscriptionPlan = new SubscriptionPlan();
        $form = $this->createForm(SubscriptionPlanType::class, $subscriptionPlan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($subscriptionPlan);
            $entityManager->flush();

            return $this->redirectToRoute('app_administrator_subscription_plan_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('administrator/subscription_plan/new.html.twig', [
            'subscription_plan' => $subscriptionPlan,
            'form' => $form,
        ]);
    }
}
