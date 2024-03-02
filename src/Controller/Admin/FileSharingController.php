<?php namespace App\Controller\Admin;

use App\Controller\Admin\Table\NotificationTable;
use App\Entity\Notification;
use App\Entity\User;
use App\Form\FileSharingType;
use App\Service\CrudTable\CrudTableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/admin/file_sharing', name: 'app_admin_file_sharing_')]
class FileSharingController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        $fileSharingForm = $this->createForm(FileSharingType::class);
        $fileSharingForm->handleRequest($request);

        if ($fileSharingForm->isSubmitted() && $fileSharingForm->isValid()) {
            exit("FORM VALI");
        }

        return $this->render('admin/file_sharing/index.html.twig', ['form' => $fileSharingForm]);
    }

}
