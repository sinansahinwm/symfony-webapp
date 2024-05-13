<?php

namespace App\Controller\Admin;

use App\Entity\Team;
use App\Entity\TeamInvite;
use App\Entity\User;
use App\Form\InviteTeamMemberType;
use App\Form\TeamEditType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function Symfony\Component\Translation\t;

#[Route('/admin/team', name: 'app_admin_team_')]
class TeamController extends AbstractController
{
    #[Route('/board', name: 'board', methods: ['GET'])]
    public function teamBoard(#[CurrentUser] User $loggedUser): Response
    {
        $usersTeam = $loggedUser->getTeam();
        if ($usersTeam === NULL) {
            $this->addFlash('pageNotificationError', t("Hiçbir takıma üye değilsiniz."));
            return $this->redirectToRoute('app_admin_dashboard');
        }
        return $this->render('admin/team/board.html.twig', ["usersTeam" => $usersTeam]);
    }

    #[Route('/inviteMember', name: 'invite_member', methods: ['GET', 'POST'])]
    public function teamInviteMember(#[CurrentUser] User $loggedUser, Request $request, EntityManagerInterface $entityManager): Response
    {
        $inviteForm = $this->createForm(InviteTeamMemberType::class);
        $inviteForm->handleRequest($request);

        if ($inviteForm->isSubmitted() && $inviteForm->isValid()) {

            // Get Form Email
            $inviteEmail = $inviteForm->getData()["email"];

            // Add Team Invite
            $myInvite = new TeamInvite();
            $myInvite->setTeam($loggedUser->getTeam());
            $myInvite->setCreatedAt(new DateTimeImmutable());
            $myInvite->setEmailAddress($inviteEmail);
            $entityManager->persist($myInvite);
            $entityManager->flush();

            // Redirect Request To Send Invite
            return $this->redirectToRoute('app_auth_send_team_invite_email', ['id' => $myInvite->getId()]);

        }

        return $this->render('admin/team/invite_member.html.twig', ['inviteForm' => $inviteForm]);
    }

    #[IsGranted('TEAM_EDIT', 'theTeam')]
    #[Route('/edit/{theTeam}', name: 'edit', methods: ['GET', 'POST'])]
    public function teamEdit(#[CurrentUser] User $loggedUser, Request $request, EntityManagerInterface $entityManager, Team $theTeam): Response
    {
        $usersTeam = $loggedUser->getTeam();
        if ($usersTeam === NULL) {
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
