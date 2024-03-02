<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\AppAdminMenuExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppAdminMenuExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('appAdminMenu', [AppAdminMenuExtensionRuntime::class, 'appAdminMenu']),
            new TwigFunction('appUserMenu', [AppAdminMenuExtensionRuntime::class, 'appUserMenu']),
        ];
    }
}
