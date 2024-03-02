<?php namespace App\Service\CrudTable;

use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\QueryBuilderProcessorInterface;
use Omines\DataTablesBundle\DataTableState;

class DisableCachingCriteriaProvider implements QueryBuilderProcessorInterface
{

    public function process(QueryBuilder $queryBuilder, DataTableState $state): void
    {
        $queryBuilder->setCacheable(FALSE);
    }
}