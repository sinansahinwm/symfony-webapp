<?php namespace App\Twig\Component;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Countable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('_notification_component')]
class NotificationComponent extends AbstractController
{
    use DefaultActionTrait;

    public $latestNotifications;
    #[LiveProp(writable: true)]
    public int $latestNotificationsCount;


    public function __construct(private NotificationRepository $notificationRepository, private Security $security, private EntityManagerInterface $entityManager)
    {
        $this->latestNotifications = $this->notificationRepository->getLatest($this->security->getUser());;
        $this->latestNotificationsCount = count($this->latestNotifications);
    }

}