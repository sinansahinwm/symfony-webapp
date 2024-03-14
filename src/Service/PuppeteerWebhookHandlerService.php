<?php namespace App\Service;

use App\Config\PuppeteerReplayStatusType;
use App\Entity\PuppeteerReplay;
use App\Repository\PuppeteerReplayRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PuppeteerWebhookHandlerService
{
    public function __construct(private PuppeteerReplayRepository $puppeteerReplayRepository, private EntityManagerInterface $entityManager)
    {
    }

    public function handleHook(array $hookBodyData): void
    {
        $dataIsValid = $this->checkBodyData($hookBodyData);
        if ($dataIsValid === TRUE) {
            $bodyInstanceID = $hookBodyData["instanceID"];
            $instanceEntity = $this->puppeteerReplayRepository->find($bodyInstanceID);
            if ($instanceEntity instanceof PuppeteerReplay) {
                $bodyPhase = $hookBodyData["phase"];
                $bodyStep = $hookBodyData["step"];
                $bodyScreenshot = $hookBodyData["screenshot"];
                $bodyContent = $hookBodyData["content"];

                // Update Replay Life Cycle
                $this->updateLifeCycle($instanceEntity, $bodyPhase);
            }
        } else {
            throw new BadRequestHttpException();
        }
    }

    private function checkBodyData(array $hookBodyData): bool
    {
        return TRUE;
    }

    private function updateLifeCycle(PuppeteerReplay $puppeteerReplay, $phase): void
    {
        $puppeteerReplayStatus = match ($phase) {
            default => PuppeteerReplayStatusType::UPLOAD,
            'beforeEachStep', 'afterEachStep' => PuppeteerReplayStatusType::PROCESSING,
            'afterAllSteps' => PuppeteerReplayStatusType::COMPLETED,
        };
        $puppeteerReplay->setStatus($puppeteerReplayStatus);
        $this->entityManager->persist($puppeteerReplay);
        $this->entityManager->flush();
    }
}