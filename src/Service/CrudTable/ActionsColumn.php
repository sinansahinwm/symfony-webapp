<?php namespace App\Service\CrudTable;

use Omines\DataTablesBundle\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ActionsColumn extends AbstractColumn
{

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function normalize(mixed $value): string
    {
        $dropdownItems = array_map(function (callable $actionCallable, $value) {
            if (is_callable($actionCallable)) {
                $callResult = call_user_func($actionCallable, [$value, $this->urlGenerator]);
                $iconDef = ($callResult->getIcon() !== NULL) ? '<i class="' . $callResult->getIcon() . ' me-1"></i> ' : ' ';
                return '<a class="dropdown-item" href="' . $callResult->getText() . '">' . $iconDef . $callResult->getText() . '</a>';
            }
            return '';
        }, $this->options["actions"], [$value]);
        return '<div class="dropdown"><button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button><div class="dropdown-menu">' . implode('', $dropdownItems) . ' </div></div>';
    }

    protected function configureOptions(OptionsResolver $resolver): static
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('actions', []);
        $resolver->setAllowedTypes('actions', ['array']);
        $resolver->setDefault('className', 'text-center');
        $resolver->setDefault('label', ' ');
        $resolver->setDefault('searchable', FALSE);
        $resolver->setDefault('globalSearchable', FALSE);
        $resolver->setDefault('orderable', FALSE);
        $resolver->setDefault('raw', FALSE);
        return $this;
    }
}
