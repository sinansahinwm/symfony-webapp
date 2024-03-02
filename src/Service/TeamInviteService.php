<?php namespace App\Service;

use App\Config\MessageBusDelays;
use App\Entity\TeamInvite;
use App\Entity\User;
use App\Message\AppEmailMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use function Symfony\Component\Translation\t;

class TeamInviteService
{

    public function __construct(private UrlGeneratorInterface $urlGenerator, private MessageBusInterface $messageBus, private EntityManagerInterface $entityManager)
    {
    }

    public function sendTeamInviteMail(TeamInvite $teamInvite): void
    {
        $inviteContext = [
            "teamName" => $teamInvite->getTeam()->getName(),
            "teamOwnerName" => $teamInvite->getTeam()->getOwner()->getDisplayName() ?? $teamInvite->getTeam()->getOwner()->getEmail()
        ];
        $inviteCallToActionContext = [
            "url" => $this->urlGenerator->generate('app_auth_accept_team_invite_email', ['id' => $teamInvite->getId(), UrlGeneratorInterface::ABSOLUTE_URL]),
            "title" => t('Daveti Kabul Et')
        ];
        $inviteEmail = new AppEmailMessage(
            'team_invite',
            $teamInvite->getUser()->getEmail(),
            t('Takım Daveti'),
            $inviteContext,
            $inviteCallToActionContext,
        );
        $this->messageBus->dispatch($inviteEmail, [new DelayStamp(MessageBusDelays::SEND_INVITE_EMAIL_AFTER_PERSISTED)]);
    }

    public function acceptTeamInviteMail(TeamInvite $teamInvite): void
    {
        // Add team collaborator.
        $userRepo = $this->entityManager->getRepository(User::class);
        $theUser = $userRepo->findOneBy(["email" => $teamInvite->getEmailAddress()]);
        $theTeam = $teamInvite->getTeam();
        $theTeam->addCollaborator($teamInvite->getUser() ?? $theUser);
        $this->entityManager->persist($theTeam);
        $this->entityManager->flush();

        // Remove Team Invite
        $this->entityManager->remove($teamInvite);
        $this->entityManager->flush();
    }
}