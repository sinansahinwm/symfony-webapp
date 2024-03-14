<?php

namespace App\Controller;

use App\Config\PuppeteerReplayStatusType;
use App\Entity\PuppeteerReplay;
use App\Form\PuppeteerReplayType;
use App\Repository\PuppeteerReplayRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/puppeteer/replay')]
class PuppeteerReplayController extends AbstractController
{
    #[Route('/', name: 'app_puppeteer_replay_index', methods: ['GET'])]
    public function index(PuppeteerReplayRepository $puppeteerReplayRepository): Response
    {
        return $this->render('puppeteer_replay/index.html.twig', [
            'puppeteer_replays' => $puppeteerReplayRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_puppeteer_replay_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $puppeteerReplay = new PuppeteerReplay();
        $form = $this->createForm(PuppeteerReplayType::class, $puppeteerReplay);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($puppeteerReplay);
            $entityManager->flush();

            return $this->redirectToRoute('app_puppeteer_replay_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('puppeteer_replay/new.html.twig', [
            'puppeteer_replay' => $puppeteerReplay,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_puppeteer_replay_show', methods: ['GET'])]
    public function show(PuppeteerReplay $puppeteerReplay): Response
    {
        return $this->render('puppeteer_replay/show.html.twig', [
            'puppeteer_replay' => $puppeteerReplay,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_puppeteer_replay_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PuppeteerReplay $puppeteerReplay, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PuppeteerReplayType::class, $puppeteerReplay);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_puppeteer_replay_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('puppeteer_replay/edit.html.twig', [
            'puppeteer_replay' => $puppeteerReplay,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_puppeteer_replay_delete', methods: ['POST'])]
    public function delete(Request $request, PuppeteerReplay $puppeteerReplay, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$puppeteerReplay->getId(), $request->request->get('_token'))) {
            $entityManager->remove($puppeteerReplay);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_puppeteer_replay_index', [], Response::HTTP_SEE_OTHER);
    }
}
