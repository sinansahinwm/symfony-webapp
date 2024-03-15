<?php namespace App\Service\CrudTable;

use Omines\DataTablesBundle\Column\TextColumn;

class ShowMoreTextColumn extends TextColumn
{
    const SLICE_THRESHOLD = 10;

    public function normalize(mixed $value): string
    {
        $normalizedParent = parent::normalize($value);
        if (strlen($normalizedParent) > self::SLICE_THRESHOLD) {
            return  "slicing" . $value;
        }
        return $value;
    }

}