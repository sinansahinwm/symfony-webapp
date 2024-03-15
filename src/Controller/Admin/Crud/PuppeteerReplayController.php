<?php

namespace App\Controller\Admin\Crud;

use App\Controller\Admin\Table\PuppeteerReplayTable;
use App\Entity\PuppeteerReplay;
use App\Form\PuppeteerReplayType;
use App\Service\CrudTable\CrudTableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
    #[Route('/show/{puppeteerReplay}', name: 'show', methods: ['GET'])]
    public function show(PuppeteerReplay $puppeteerReplay): Response
    {
        return $this->render('admin/crud/puppeteer_replay/show.html.twig', [
            'puppeteer_replay' => $puppeteerReplay,
        ]);
    }

    #[IsGranted("PUPPETEER_REPLAY_DELETE", 'puppeteerReplay')]
    #[Route('/delete/{puppeteerReplay}', name: 'delete')]
    public function delete(Request $request, PuppeteerReplay $puppeteerReplay, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $puppeteerReplay->getId(), $request->request->get('_token'))) {
            $entityManager->remove($puppeteerReplay);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_puppeteer_replay_index', [], Response::HTTP_SEE_OTHER);
    }
}
