<?php namespace App\Service\CrudTable;

use Omines\DataTablesBundle\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BadgeColumn extends AbstractColumn
{

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function normalize(mixed $value): string
    {
        $badgeLabelCallable = $this->options["label"];
        $finalBadgeLabel = is_callable($badgeLabelCallable) ? call_user_func($badgeLabelCallable, $value) : $value;

        return '<span class="badge rounded-pill bg-' . $this->options["type"] . '">' . $finalBadgeLabel . '</span>';
    }

    protected function configureOptions(OptionsResolver $resolver): static
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('type', 'secondary');
        $resolver->setAllowedTypes('type', ['null', 'string']);

        $resolver->setDefault('label', null);
        $resolver->setAllowedTypes('label', ['null', 'callable']);

        $resolver->setDefault('className', 'text-center');
        $resolver->setDefault('searchable', FALSE);
        $resolver->setDefault('globalSearchable', FALSE);
        $resolver->setDefault('orderable', FALSE);
        $resolver->setDefault('raw', FALSE);
        return $this;
    }
}
