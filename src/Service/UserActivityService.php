<?php namespace App\Service;

use App\Config\UserActivityType;
use App\Entity\User;
use App\Entity\UserActivity;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserActivityService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function releaseActivity(User|UserInterface $user, string $activityType): UserActivity
    {
        $myActivity = new UserActivity();
        $myActivity->setUser($user);
        $myActivity->setActivityType($activityType);
        $myActivity->setCreatedAt(new DateTimeImmutable());
        $this->entityManager->persist($myActivity);
        $this->entityManager->flush();
        return $myActivity;
    }

}