<?php namespace App\Controller\Admin\Select;

use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SelectController extends AbstractController
{
    const CALLBACK_PATH_PREFIX = '/admin/callback/select2/';

    public function getCallbackResponse(QueryBuilder $queryBuilder, ?callable $textGetter = NULL, ?callable $idGetter = NULL): Response
    {
        $queryResult = $queryBuilder->getQuery()->getResult();
        $resultsArray = [];
        foreach ($queryResult as $result) {
            $resultsArray[] = [
                "id" => $idGetter === NULL ? $result->getId() : call_user_func($idGetter, $result),
                "text" => $textGetter === NULL ? $result : call_user_func($textGetter, $result),
            ];
        }
        return new JsonResponse([
            "results" => $resultsArray,
            "pagination" => [
                "more" => FALSE
            ]
        ]);
    }
}