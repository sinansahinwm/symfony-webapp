<?php namespace App\Controller\Admin\Crud;

use App\Controller\Admin\Table\NotificationTable;
use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use App\Service\CrudTable\CrudTableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/notification', name: 'app_admin_notification_')]
class NotificationController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, CrudTableService $crudTableService): Response
    {

        $notificationTable = $crudTableService->createFromFQCN($request, NotificationTable::class);
        return $this->render('admin/notification/index.html.twig', ['notificationTable' => $notificationTable]);
    }

    #[IsGranted("NOTIFICATION_MARK_AS_READ", 'notification')]
    #[Route('/read/{notification}', name: 'mark_as_read')]
    public function markAsRead(Notification $notification, #[CurrentUser] User $loggedUser, EntityManagerInterface $entityManager): Response
    {
        if ($notification->getToUser()->getUserIdentifier() === $loggedUser->getUserIdentifier()) {
            $notification->setIsRead(TRUE);
            $entityManager->persist($notification);
        }

        $entityManager->flush();
        return $this->redirectToRoute('app_admin_notification_index');
    }

    #[Route('/read_all', name: 'mark_as_read_all')]
    public function markAsReadAll(#[CurrentUser] User $loggedUser, EntityManagerInterface $entityManager, NotificationRepository $notificationRepository): Response
    {
        $usersNotificationsUnread = $notificationRepository->findBy(["to_user" => $loggedUser, "is_read" => FALSE]);
        foreach ($usersNotificationsUnread as $index => $usersNotification) {
            $usersNotification->setIsRead(TRUE);
            $entityManager->persist($usersNotification);
        }

        $entityManager->flush();
        return $this->redirectToRoute('app_admin_notification_index');
    }

    #[IsGranted("NOTIFICATION_DELETE", 'notification')]
    #[Route('/remove/{notification}', name: 'remove')]
    public function removeNotification(Notification $notification, #[CurrentUser] User $loggedUser, EntityManagerInterface $entityManager): Response
    {
        if ($notification->getToUser()->getUserIdentifier() === $loggedUser->getUserIdentifier()) {
            $entityManager->remove($notification);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_admin_notification_index');
    }

    #[IsGranted("NOTIFICATION_MARK_AS_READ", 'notification')]
    #[Route('/show/{notification}', name: 'show')]
    public function showNotification(Notification $notification, #[CurrentUser] User $loggedUser, EntityManagerInterface $entityManager): Response
    {
        $notificationUrl = $notification->getUrl();
        if($notificationUrl !== NULL){
            return $this->redirect($notificationUrl);
        }
        return $this->redirectToRoute('app_admin_notification_index');
    }

}