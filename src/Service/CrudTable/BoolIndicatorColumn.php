<?php namespace App\Service\CrudTable;

use Omines\DataTablesBundle\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BoolIndicatorColumn extends AbstractColumn
{
    public function normalize(mixed $value): string
    {
        return match ($value) {
            true => '<span class="badge badge-dot bg-success"></span>',
            default => '<span class="badge badge-dot bg-danger"></span> ',
        };
    }

    protected function configureOptions(OptionsResolver $resolver): static
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('label', ' ');
        $resolver->setDefault('className', 'text-center');
        $resolver->setDefault('searchable', FALSE);
        $resolver->setDefault('globalSearchable', FALSE);
        $resolver->setDefault('orderable', FALSE);
        $resolver->setDefault('raw', FALSE);
        return $this;
    }
}
