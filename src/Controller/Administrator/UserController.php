<?php namespace App\Controller\Administrator;

use App\Controller\Admin\Table\UserTable;
use App\Service\CrudTable\CrudTableService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/administrator/user', name: 'app_administrator_user_')]
class UserController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(Request $request, CrudTableService $crudTableService): Response
    {
        $userTable = $crudTableService->createFromFQCN($request, UserTable::class);
        return $this->render('administrator/user/index.html.twig', [
            'userTable' => $userTable,
        ]);
    }

}
