<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\CrudTableLanguageExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CrudTableLanguageExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('datatable_language', [CrudTableLanguageExtensionRuntime::class, 'getCrudTableLanguage']),
        ];
    }
}
