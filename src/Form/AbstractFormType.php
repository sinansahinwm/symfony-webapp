<?php namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Contracts\Translation\TranslatorInterface;

class AbstractFormType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function t(string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }
}