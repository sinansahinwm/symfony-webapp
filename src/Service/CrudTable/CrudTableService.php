<?php namespace App\Service\CrudTable;

use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class CrudTableService extends AbstractController
{

    public function __construct(private DataTableFactory $dataTableFactory)
    {
    }

    public function isCallback(Request $request): Response
    {
        return $this->dataTableFactory->createFromType($request->get("_fqcn"), [], ["stateSave" => $request->get("_fqcn")])->handleRequest($request)->getResponse();
    }

    public function createFromFQCN(Request $request, string $tableClassFQCN): DataTable
    {
        return $this->dataTableFactory->createFromType($tableClassFQCN, [], ["stateSave" => $tableClassFQCN])->handleRequest($request);
    }


}