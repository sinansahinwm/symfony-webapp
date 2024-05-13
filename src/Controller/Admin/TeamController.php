<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\TeamEditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use function Symfony\Component\Translation\t;

#[Route('/admin/team', name: 'app_admin_team_')]
class TeamController extends AbstractController
{
    #[Route('/board', name: 'board', methods: ['GET'])]
    public function teamBoard(#[CurrentUser] User $loggedUser): Response
    {
        $usersTeam = $loggedUser->getTeam();
        if($usersTeam === NULL){
            $this->addFlash('pageNotificationError', t("Hiçbir takıma üye değilsiniz."));
            return $this->redirectToRoute('app_admin_dashboard');
        }
        return $this->render('admin/team/board.html.twig', ["usersTeam" => $usersTeam]);
    }

    #[Route('/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function teamEdit(#[CurrentUser] User $loggedUser, Request $request, EntityManagerInterface $entityManager): Response
    {
        $usersTeam = $loggedUser->getTeam();
        if($usersTeam === NULL){
            $this->addFlash('pageNotificationError', t("Hiçbir takıma üye değilsiniz."));
            return $this->redirectToRoute('app_admin_dashboard');
        }

        $teamForm = $this->createForm(TeamEditType::class, $usersTeam);
        $teamForm->handleRequest($request);

        if ($teamForm->isSubmitted() && $teamForm->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_team_board', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/team/edit.html.twig', [
            'team' => $usersTeam,
            'form' => $teamForm,
        ]);
    }

}
