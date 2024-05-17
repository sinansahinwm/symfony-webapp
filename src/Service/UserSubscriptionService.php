<?php namespace App\Service;

use App\Entity\SubscriptionPlan;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserSubscriptionService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function subscribeUser(User $user, SubscriptionPlan $subscriptionPlan): void
    {
        // TODO : Add Subscription

        // Set User's Subscription Plan
        $user->setSubscriptionPlan($subscriptionPlan);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

    }

}