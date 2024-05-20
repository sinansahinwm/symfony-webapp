<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\SubscriptionPlanExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class SubscriptionPlanExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('planDaysRemaining', [SubscriptionPlanExtensionRuntime::class, 'planDaysRemaining']),
        ];
    }
}
