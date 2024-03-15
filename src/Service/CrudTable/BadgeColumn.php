<?php namespace App\Service\CrudTable;

use Omines\DataTablesBundle\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BadgeColumn extends AbstractColumn
{
    public function normalize(mixed $value): string
    {
        $badgeLabelCallable = $this->options["content"];
        $badgeTypeCallable = $this->options["type"];
        $finalBadgeLabel = is_callable($badgeLabelCallable) ? call_user_func($badgeLabelCallable, $value) : $value;
        $finalBadgeType = is_callable($badgeTypeCallable) ? call_user_func($badgeTypeCallable, $value) : $value;

        return '<span class="badge rounded-pill bg-' . $finalBadgeType . '">' . $finalBadgeLabel . '</span>';
    }

    protected function configureOptions(OptionsResolver $resolver): static
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('type', null);
        $resolver->setAllowedTypes('type', ['null', 'callable']);

        $resolver->setDefault('content', null);
        $resolver->setAllowedTypes('content', ['null', 'callable']);

        $resolver->setDefault('label', ' ');
        $resolver->setDefault('className', 'text-center');
        $resolver->setDefault('searchable', FALSE);
        $resolver->setDefault('globalSearchable', FALSE);
        $resolver->setDefault('orderable', FALSE);
        $resolver->setDefault('raw', FALSE);
        return $this;
    }
}
