<?php namespace App\Controller\Admin;

use App\Config\MessageBusDelays;
use App\Controller\Admin\Table\NotificationTable;
use App\Entity\AbstractFile;
use App\Entity\Notification;
use App\Entity\User;
use App\Form\FileSharingType;
use App\Form\UserType;
use App\Message\AppEmailMessage;
use App\Repository\UserRepository;
use App\Service\CrudTable\CrudTableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use function Symfony\Component\Translation\t;

#[Route('/admin/file_sharing', name: 'app_admin_file_sharing_')]
class FileSharingController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, EntityManagerInterface $entityManager, MessageBusInterface $messageBus, #[CurrentUser] User $loggedUser, UrlGeneratorInterface $urlGenerator): Response
    {

        $abstractFile = new AbstractFile();
        $fileSharingForm = $this->createForm(FileSharingType::class, $abstractFile);
        $fileSharingForm->handleRequest($request);

        if ($loggedUser->getTeam() === NULL) {
            $this->addFlash('pageNotificationError', t("Herhangi bir takımda olmadığınız için dosya paylaşamazsınız."));
        }

        if ($fileSharingForm->isSubmitted() && $fileSharingForm->isValid()) {

            $sharingUsers = $fileSharingForm->get('toUser')->getData();
            $filePassword = $fileSharingForm->get('password')->getData();

            // Persis File
            $entityManager->persist($abstractFile);
            $entityManager->flush();

            // Send Emails
            foreach ($sharingUsers as $sharingUser) {

                $generatedDownloadURL = $urlGenerator->generate('app_admin_storage', ["fileName" => $abstractFile->getFileName(), "download" => TRUE], UrlGeneratorInterface::ABSOLUTE_URL);
                if ($filePassword !== NULL) {
                    $generatedDownloadURL = $urlGenerator->generate('app_admin_storage', ["fileName" => $abstractFile->getFileName(), "download" => TRUE, "filePassword" => $filePassword], UrlGeneratorInterface::ABSOLUTE_URL);
                }
                $emailContext = [
                    "publisherName" => $loggedUser->getDisplayName() ?? $loggedUser->getEmail()
                ];
                $emailCTA = [
                    "title" => t("Dosyayı İndir"),
                    "url" => $generatedDownloadURL
                ];
                $myEmailMessage = new AppEmailMessage('file_sharing', $sharingUser->getEmail(), t('Dosya Paylaşıldı'), $emailContext, $emailCTA);
                $messageBus->dispatch($myEmailMessage, [new DelayStamp(MessageBusDelays::SEND_FILE_SHARING_EMAIL_AFTER_PERSISTED)]);
            }

            $this->addFlash('pageNotificationSuccess', t('Dosyanız başarıyla yüklendi ve kişilere e-posta olarak  gönderildi.'));
            $this->redirectToRoute('app_admin_file_sharing_index');
        }

        return $this->render('admin/file_sharing/index.html.twig', ['form' => $fileSharingForm]);
    }

}
