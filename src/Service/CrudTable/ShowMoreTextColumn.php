<?php namespace App\Service\CrudTable;

use Omines\DataTablesBundle\Column\TextColumn;

class ShowMoreTextColumn extends TextColumn
{
    public function normalize(mixed $value): string
    {
        $normalizedParent = parent::normalize($value);
        return "show mored text" . $value;
    }

}