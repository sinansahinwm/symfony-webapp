<?php namespace App\Service;

use App\Config\NotificationPriorityType;
use App\Entity\Notification;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class NotificationService
{

    private string $message;


    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function release(User|UserInterface $toUser, $actionUrl = NULL ,$priority = NotificationPriorityType::NORMAL): Notification
    {
        $myNotification = new Notification();
        $myNotification->setIsRead(FALSE);
        $myNotification->setPriority($priority);
        $myNotification->setToUser($toUser);
        $myNotification->setContent($this->message);
        $myNotification->setUrl($actionUrl);
        $myNotification->setCreatedAt(new DateTimeImmutable());
        $this->entityManager->persist($myNotification);
        $this->entityManager->flush();
        return $myNotification;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }


}