<?php namespace App\Controller\Admin\Table;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;

class TableAbstractController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function t(string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }
}