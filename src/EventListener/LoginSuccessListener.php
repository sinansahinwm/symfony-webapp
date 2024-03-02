<?php namespace App\EventListener;

use App\Entity\TeamInvite;
use App\Repository\TeamInviteRepository;
use App\Service\NotificationService;
use App\Service\TeamInviteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Contracts\Translation\TranslatorInterface;


#[AsEventListener(LoginSuccessEvent::class, method: 'onLoginSuccess')]
class LoginSuccessListener
{

    public function __construct(private TeamInviteRepository $teamInviteRepository, private EntityManagerInterface $entityManager, private TeamInviteService $teamInviteService, private NotificationService $notificationService, private TranslatorInterface $translator)
    {
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $eventUser = $event->getUser();

        // Check For Invited User
        $theInvite = $this->teamInviteRepository->findOneBy(["email_address" => $eventUser->getUserIdentifier()]);
        if ($theInvite instanceof TeamInvite) {
            $theInvite->setUser($eventUser);
            $theInvite->setEmailAddress(NULL);
            $this->entityManager->persist($theInvite);
            $this->entityManager->flush();
            $this->teamInviteService->sendTeamInviteMail($theInvite);

            // Send Team Invite Inner App Notification
            $translatedNotificationMessage = $this->translator->trans("Bir takım daveti aldınız. Takım davetini kabul etmek için e-posta kutunuzu kontrol edin.");
            $this->notificationService->setMessage($translatedNotificationMessage)->release($eventUser);

        }

    }

}