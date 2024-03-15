<?php namespace App\Service\CrudTable;

use Omines\DataTablesBundle\Column\TextColumn;

class ShowMoreTextColumn extends TextColumn
{
    const SLICE_THRESHOLD = 10;
    const SLICE_SHOW_MORE_DELIMETER = '...';
    const SLICE_SHOW_MORE_SPAN_CLASS = ['text-primary'];

    public function normalize(mixed $value): string
    {
        $normalizedParent = parent::normalize($value);
        if (strlen($normalizedParent) > self::SLICE_THRESHOLD && is_string($value)) {
            $slicedText = mb_substr($value, 0, self::SLICE_THRESHOLD);
            return $slicedText . $this->getSlicedPostFix($value);
        }
        return $value;
    }

    private function getSlicedPostFix(string $value): string
    {
        return '<span class="showMoreSpan ' . implode(' ', self::SLICE_SHOW_MORE_SPAN_CLASS) . '" data-content="' . $value . '">' . self::SLICE_SHOW_MORE_DELIMETER . '</span>';
    }

}