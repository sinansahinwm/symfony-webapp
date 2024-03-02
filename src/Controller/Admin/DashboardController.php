<?php namespace App\Controller\Admin;

use App\Entity\Team;
use App\Entity\TeamOwnership;
use App\Entity\User;
use App\Form\TeamType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use function Symfony\Component\Translation\t;

#[Route(path: '/admin', name: 'app_admin_')]
class DashboardController extends AbstractController
{
    #[Route(path: '/dashboard', name: 'dashboard')]
    public function index(Request $request, EntityManagerInterface $entityManager, #[CurrentUser] User $loggedUser): Response
    {
        $myTeam = new Team();
        $teamForm = $this->createForm(TeamType::class, $myTeam);
        $teamForm->handleRequest($request);
        if ($teamForm->isSubmitted() && $teamForm->isValid()) {
            if ($loggedUser->getTeam() instanceof Team) {
                $this->addFlash('pageNotificationError', t("Zaten bir takımınız var. Yeni takım kuramazsınız."));
            } else {

                // Persist Team
                $myTeam->setCreatedAt(new DateTimeImmutable());
                $entityManager->persist($myTeam);
                $entityManager->flush();

                // Persist User
                $loggedUser->setTeam($myTeam);
                $entityManager->persist($loggedUser);
                $entityManager->flush();

                // Persist Ownership
                $userOwnership = new TeamOwnership();
                $userOwnership->setUser($loggedUser);
                $userOwnership->setTeam($myTeam);
                $entityManager->flush();

            }
        }
        return $this->render('admin/dashboard/index.html.twig', [
            'teamForm' => $teamForm
        ]);
    }
}