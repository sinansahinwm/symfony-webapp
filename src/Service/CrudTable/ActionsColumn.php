<?php namespace App\Service\CrudTable;

use Omines\DataTablesBundle\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ActionsColumn extends AbstractColumn
{

    private $subjectValue;

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function createDropdownItem(mixed $theAction): string
    {
        if (is_callable($theAction)) {
            $callResult = call_user_func($theAction, $this->subjectValue, $this->urlGenerator);
            $iconDef = ($callResult->getIcon() !== NULL) ? '<i class="' . $callResult->getIcon() . ' me-1"></i> ' : ' ';
            return '<a class="dropdown-item" href="' . $callResult->getUrl() . '">' . $iconDef . $callResult->getText() . '</a>';
        } else {
            return strval($this->subjectValue);
        }
    }

    public function normalize(mixed $value): string
    {
        $this->subjectValue = $value;
        $createdDropdownItems = array_map([$this, 'createDropdownItem'], $this->options["actions"]);
        return '<div class="dropdown"><button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button><div class="dropdown-menu">' . implode('', $createdDropdownItems) . ' </div></div>';
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
