<?php namespace App\Controller\Admin\Select;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface SelectControllerInterface
{

    public function callback(Request $request): Response;

    public function queryBuilder(EntityRepository $entityRepository, ?string $searchKeyword = NULL): QueryBuilder;

}