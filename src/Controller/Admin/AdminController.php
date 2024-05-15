<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Table\NotificationTable;
use App\Controller\Admin\Table\UserTable;
use App\Service\CrudTable\CrudTableService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/admin', name: 'app_admin_')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, CrudTableService $crudTableService): Response
    {
        $userTable = $crudTableService->createFromFQCN($request, UserTable::class);

        return $this->render('admin/admin/index.html.twig', [
            'userTable' => $userTable,
        ]);
    }
}
