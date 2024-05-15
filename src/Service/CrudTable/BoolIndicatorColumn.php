<?php namespace App\Service\CrudTable;

use Omines\DataTablesBundle\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BoolIndicatorColumn extends AbstractColumn
{
    public function normalize(mixed $value): string
    {

        $trueBadge = "success";
        $defaultBadge = "danger";

        // Check Reversed Option
        if ($this->options["reverse"] === TRUE) {
            $trueBadge = "danger";
            $defaultBadge = "success";
        }

        return match ($value) {
            true => '<span class="badge badge-dot bg-' . $trueBadge . '"></span>',
            default => '<span class="badge badge-dot bg-' . $defaultBadge . '"></span> ',
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

        $resolver->setDefault('reverse', FALSE);
        $resolver->setAllowedTypes('reverse', ['null', 'bool']);

        return $this;
    }
}
