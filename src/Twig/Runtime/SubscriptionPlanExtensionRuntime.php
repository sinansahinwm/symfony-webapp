<?php

namespace App\Twig\Runtime;

use App\Entity\User;
use App\Service\UserSubscriptionService;
use Twig\Extension\RuntimeExtensionInterface;

class SubscriptionPlanExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function planDaysRemaining(User $user): int
    {
        return UserSubscriptionService::planDaysRemaining($user);
    }
}
