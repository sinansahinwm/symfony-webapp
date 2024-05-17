<?php namespace App\Service;

use App\Entity\SubscriptionPlan;
use App\Entity\User;
use App\Entity\UserPayment;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class UserSubscriptionService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function subscribeUserAfterPayment(User $user, SubscriptionPlan $subscriptionPlan, UserPayment $userPaymentProof): void
    {
        $dtNow = new DateTime();
        $subscriptionPlanUsableDays = $subscriptionPlan->getPaymentInterval();
        $userPlanAlreadyHave = $user->getSubscriptionPlan();

        // Add User Older Plan Days (If Exist)
        if ($userPlanAlreadyHave !== NULL) {
            if ($userPlanAlreadyHave->getId() === $subscriptionPlan->getId()) {
                $validUntilAt = $user->getSubscriptionPlanValidUntil();
                if ($validUntilAt !== NULL) {
                    $timeNowX = new DateTimeImmutable();
                    if ($timeNowX->getTimestamp() < $validUntilAt->getTimestamp()) {
                        $timeDiffSeconds = $timeNowX->getTimestamp() - $validUntilAt->getTimestamp();
                        $timeDiffDays = abs(floor(($timeDiffSeconds / 3600) / 24));
                        $subscriptionPlanUsableDays += $timeDiffDays;
                    }
                }
            }
        }

        // Modify Plan Date -> Calculate Plan Ends At
        $modifiedPlanDT = $dtNow->modify('+' . $subscriptionPlanUsableDays . ' day');
        $userPlanValidUntilAt = DateTimeImmutable::createFromMutable($modifiedPlanDT);

        // Set User Plan Details
        $user->setSubscriptionPlanValidUntil($userPlanValidUntilAt);
        $user->setSubscriptionPlan($subscriptionPlan);
        $user->setTrialPeriodUsed(TRUE); // Trial is only usable before first payment.

        $this->entityManager->persist($user);
        $this->entityManager->flush();

    }

}