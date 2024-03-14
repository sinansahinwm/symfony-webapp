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
            $bodyPhase = $hookBodyData["phase"];

            $instanceEntity = $this->puppeteerReplayRepository->find($bodyInstanceID);
            if ($instanceEntity instanceof PuppeteerReplay) {

                // Update Replay Life Cycle
                $this->updateLifeCycle($instanceEntity, $bodyPhase);

                // $bodyStep = $hookBodyData["step"];
                // $bodyScreenshot = $hookBodyData["screenshot"];
                // $bodyContent = $hookBodyData["content"];


            } else {
                throw new BadRequestHttpException();
            }
        } else {
            throw new BadRequestHttpException();
        }
    }

    private function checkBodyData(array $hookBodyData): bool
    {
        return isset($hookBodyData["phase"]) && isset($hookBodyData["instanceID"]);
    }

    private function updateLifeCycle(PuppeteerReplay $puppeteerReplay, $phase): void
    {
        $puppeteerReplayStatus = match ($phase) {
            default => PuppeteerReplayStatusType::UPLOAD,
            'beforeEachStep', 'afterEachStep' => PuppeteerReplayStatusType::PROCESSING,
            'afterAllSteps' => PuppeteerReplayStatusType::COMPLETED,
            'error' => PuppeteerReplayStatusType::ERROR,
        };
        $puppeteerReplay->setStatus($puppeteerReplayStatus);
        $this->entityManager->persist($puppeteerReplay);
        $this->entityManager->flush();
    }
}