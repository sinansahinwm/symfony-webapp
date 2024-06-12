<?php namespace App\Service\CrudTable;

use Omines\DataTablesBundle\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Symfony\Component\Translation\t;

class ImageColumn extends AbstractColumn
{
    public function normalize(mixed $value): string
    {
        $imageURLCallable = $this->options["url"];
        $finalImageURL = is_callable($imageURLCallable) ? call_user_func($imageURLCallable, $value) : $value;
        $finalImageSize = $this->options["size"] ?? 'md';
        $finalImageHref = $this->options["href"] ?? $value;
        return '<a href="' . $finalImageHref . '" target="_blank"><div class="avatar avatar-' . strtolower($finalImageSize) . ' rounded p-1"><img alt="' . t("Ürün") . '" src="' . $finalImageURL . '"></div></a>';
    }

    protected function configureOptions(OptionsResolver $resolver): static
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('size', null);
        $resolver->setAllowedTypes('size', ['null', 'string']);

        $resolver->setDefault('url', null);
        $resolver->setAllowedTypes('url', ['null', 'callable']);

        $resolver->setDefault('href', null);
        $resolver->setAllowedTypes('href', ['null', 'callable']);

        $resolver->setDefault('label', t("Görsel"));
        $resolver->setDefault('className', 'text-center d-flex justify-content-center rounded');
        $resolver->setDefault('searchable', FALSE);
        $resolver->setDefault('globalSearchable', FALSE);
        $resolver->setDefault('orderable', FALSE);
        $resolver->setDefault('raw', FALSE);
        return $this;
    }
}
