<?php namespace App\Service\CrudTable;

use Omines\DataTablesBundle\Column\TextColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShowMoreTextColumn extends TextColumn
{
    const SLICE_THRESHOLD = 20;
    const SLICE_SHOW_MORE_DELIMETER = '...';
    const SLICE_SHOW_MORE_SPAN_CLASS = ['text-primary'];

    public function normalize(mixed $value): string
    {
        $sliceSizeThreshold = $this->options["slice"] ?? self::SLICE_THRESHOLD;
        $normalizedParent = parent::normalize($value);
        if (strlen($normalizedParent) > $sliceSizeThreshold && is_string($value)) {
            $slicedText = mb_substr($value, 0, $sliceSizeThreshold);
            return $slicedText . $this->getSlicedPostFix($value);
        }
        return $value;
    }

    private function getSlicedPostFix(string $value): string
    {
        return '<span class="showMoreSpan ' . implode(' ', self::SLICE_SHOW_MORE_SPAN_CLASS) . '" data-content="' . $value . '">' . self::SLICE_SHOW_MORE_DELIMETER . '</span>';
    }

    protected function configureOptions(OptionsResolver $resolver): static
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('slice', null);
        $resolver->setAllowedTypes('slice', ['null', 'int']);

        return $this;
    }

}