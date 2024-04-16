<?php

namespace App\Controller\Admin\Crud;

use App\Config\PuppeteerReplayStatusType;
use App\Controller\Admin\Table\PuppeteerReplayTable;
use App\Controller\Webhook\PuppeteerReplayerWebhook;
use App\Entity\PuppeteerReplay;
use App\Entity\PuppeteerReplayHookRecord;
use App\Form\PuppeteerReplayType;
use App\Repository\PuppeteerReplayHookRecordRepository;
use App\Service\CrudTable\CrudTableService;
use App\Service\PuppeteerReplayService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Vich\UploaderBundle\Storage\StorageInterface;
use function Symfony\Component\Translation\t;

#[Route('/admin/puppeteer/replay', name: 'app_admin_puppeteer_replay_')]
class PuppeteerReplayController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, CrudTableService $crudTableService): Response
    {
        $notificationTable = $crudTableService->createFromFQCN($request, PuppeteerReplayTable::class);
        return $this->render('admin/crud/puppeteer_replay/index.html.twig', ['puppeteerReplayTable' => $notificationTable]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $puppeteerReplay = new PuppeteerReplay();
        $form = $this->createForm(PuppeteerReplayType::class, $puppeteerReplay);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($puppeteerReplay);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_puppeteer_replay_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/crud/puppeteer_replay/new.html.twig', [
            'puppeteer_replay' => $puppeteerReplay,
            'form' => $form,
        ]);
    }

    #[IsGranted("PUPPETEER_REPLAY_SHOW", 'puppeteerReplay')]
    #[Route('/show/{puppeteerReplay}/{hook}', name: 'show', defaults: ['hook' => NULL], methods: ['GET'])]
    public function show(PuppeteerReplay $puppeteerReplay, ?PuppeteerReplayHookRecord $hook = NULL): Response
    {
        return $this->render('admin/crud/puppeteer_replay/show.html.twig', [
            'puppeteer_replay' => $puppeteerReplay,
            'hook' => $hook
        ]);
    }

    #[IsGranted("PUPPETEER_REPLAY_DELETE", 'puppeteerReplay')]
    #[Route('/delete/{puppeteerReplay}', name: 'delete')]
    public function delete(Request $request, PuppeteerReplay $puppeteerReplay, EntityManagerInterface $entityManager, PuppeteerReplayHookRecordRepository $puppeteerReplayHookRecordRepository): Response
    {

        // Check Status
        if (($puppeteerReplay->getStatus() === PuppeteerReplayStatusType::PROCESSING) or ($puppeteerReplay->getStatus() === PuppeteerReplayStatusType::UPLOAD)) {
            $this->addFlash('pageNotificationError', t('Bu öge şu anda silinemez. Lütfen birkaç dakika bekleyin ve tekrar deneyin.'));
        } else {

            // Remove Records
            $hookRecords = $puppeteerReplay->getPuppeteerReplayHookRecords();
            foreach ($hookRecords as $hookRecord) {
                $entityManager->remove($hookRecord);
                $entityManager->flush();
            }

            // Remove PuppeteerReplay
            $entityManager->remove($puppeteerReplay);
            $entityManager->flush();

        }

        return $this->redirectToRoute('app_admin_puppeteer_replay_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/iframe/{hook}', name: 'iframe')]
    public function iframe(PuppeteerReplayHookRecord $hook): Response
    {
        return new Response($hook->getContent());
    }


    #[IsGranted("PUPPETEER_REPLAY_SHOW", 'puppeteerReplay')]
    #[Route('/rereplay/{puppeteerReplay}', name: 'rereplay')]
    public function rereplay(PuppeteerReplay $puppeteerReplay, EntityManagerInterface $entityManager, StorageInterface $storage, PuppeteerReplayService $puppeteerReplayService, PuppeteerReplayerWebhook $puppeteerReplayerWebhook): Response
    {
        // Check Status
        if (($puppeteerReplay->getStatus() === PuppeteerReplayStatusType::PROCESSING) or ($puppeteerReplay->getStatus() === PuppeteerReplayStatusType::UPLOAD)) {
            $this->addFlash('pageNotificationError', t('Bu öge şu anda yeniden başlatılamaz. Lütfen birkaç dakika bekleyin ve tekrar deneyin.'));
        } else {
            // Remove Old Step Hook Records
            $hookRecords = $puppeteerReplay->getPuppeteerReplayHookRecords();
            foreach ($hookRecords as $hookRecord) {
                $entityManager->remove($hookRecord);
                $entityManager->flush();
            }

            // Clear Status
            $puppeteerReplay->setStatus(PuppeteerReplayStatusType::UPLOAD);
            $entityManager->persist($puppeteerReplay);
            $entityManager->flush();

            // Add New Notification
            $resolvedFilePath = $storage->resolvePath($puppeteerReplay);
            $replayEnvelope = $puppeteerReplayService
                ->setRecordPath($resolvedFilePath)
                ->setWebhook($puppeteerReplayerWebhook)
                ->setInstanceID($puppeteerReplay->getId())
                ->play();

            $this->addFlash('pageNotificationSuccess', t('Kayıt yeniden çalıştırma başarılı.'));

        }
        return $this->redirectToRoute('app_admin_puppeteer_replay_index', [], Response::HTTP_SEE_OTHER);
    }

    #[IsGranted("PUPPETEER_REPLAY_SHOW", 'puppeteerReplay')]
    #[Route('/downloadsteps/{puppeteerReplay}', name: 'downloadsteps')]
    public function downloadsteps(PuppeteerReplay $puppeteerReplay, StorageInterface $storage): Response
    {
        $resolvedPath = $storage->resolvePath($puppeteerReplay);
        return new BinaryFileResponse($resolvedPath);
    }

}
