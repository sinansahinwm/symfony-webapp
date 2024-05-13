<?php namespace App\Service;

use App\Config\MessageBusDelays;
use App\Entity\TeamInvite;
use App\Entity\User;
use App\Message\AppEmailMessage;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use function Symfony\Component\Translation\t;

class TeamInviteService
{

    public function __construct(private UrlGeneratorInterface $urlGenerator, private MessageBusInterface $messageBus, private EntityManagerInterface $entityManager, private UserRepository $userRepository)
    {
    }

    public function sendTeamInviteMail(TeamInvite $teamInvite): void
    {
        $teamOwner = $this->userRepository->find($teamInvite->getTeam()->getOwnerId());

        $inviteContext = [
            "teamName" => $teamInvite->getTeam()->getName(),
            "teamOwnerName" => $teamOwner->getDisplayName() ?? $teamOwner->getEmail()
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
        $teamInviteUser = $teamInvite->getUser();
        if ($teamInviteUser) {
            $teamInviteUser->setTeam($teamInvite->getTeam());
            $this->entityManager->persist($teamInviteUser);
            $this->entityManager->flush();
        }
    }
}