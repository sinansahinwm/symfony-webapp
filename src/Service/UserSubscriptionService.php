<?php namespace App\Service;

use App\Config\MessageBusDelays;
use App\Entity\SubscriptionPlan;
use App\Entity\User;
use App\Entity\UserPayment;
use App\Message\AppEmailMessage;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserSubscriptionService
{
    public function __construct(private EntityManagerInterface $entityManager, private MessageBusInterface $messageBus, private TranslatorInterface $translator, private UrlGeneratorInterface $urlGenerator)
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

        // Send Email After Checkout & Subscribing
        $this->releaseEmailAfterSubscribing($user);

    }

    private function releaseEmailAfterSubscribing(User $user): void
    {

        $emailContext = [
            'theUser' => $user,
            'theSubscriptionPlan' => $user->getSubscriptionPlan()
        ];

        $emailCTA = [
            'title' => $this->translator->trans("Profilime Git"),
            'url' => $this->urlGenerator->generate('app_admin_dashboard', [],UrlGeneratorInterface::ABSOLUTE_URL),
        ];

        $myEmail = new AppEmailMessage(
            'user_subscribed',
            $user->getEmail(),
            $this->translator->trans('Abonelik Planı Satın Alındı'),
            $emailContext,
            $emailCTA
        );

        $this->messageBus->dispatch($myEmail, [new DelayStamp(MessageBusDelays::SEND_SUBSCRIBED_EMAIL_AFTER_USER_SUBSCRIBED)]);

    }

    public static function planDaysRemaining(User $user): int
    {
        $daysRemaining = 0;
        $subscriptionPlan = $user->getSubscriptionPlan();
        if ($subscriptionPlan instanceof SubscriptionPlan) {

            $timeNow = new DateTime();
            $planValidUntilAt = $user->getSubscriptionPlanValidUntil();
            $timeDiffSeconds = $planValidUntilAt->getTimestamp() - $timeNow->getTimestamp();
            $timeDiffDays = ceil(($timeDiffSeconds / 3600) / 24);
            if ($timeDiffDays >= 0) {
                $daysRemaining = $timeDiffDays;
            }
        }
        return $daysRemaining;
    }

}