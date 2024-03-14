<?php namespace App\Service;

use App\Config\PuppeteerReplayStatusType;
use App\Entity\PuppeteerReplay;
use App\Entity\PuppeteerReplayHookRecord;
use App\Repository\PuppeteerReplayRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
                $puppeteerReplayWithRefreshedStatus = $this->updateLifeCycle($instanceEntity, $bodyPhase, ($bodyPhase === "error") ? $hookBodyData["error"] : NULL);

                // Add State If Needed
                $this->saveHookDataIfStatusAcceptable($puppeteerReplayWithRefreshedStatus, $hookBodyData);

            } else {
                throw new BadRequestHttpException();
            }
        } else {
            throw new BadRequestHttpException();
        }
    }

    private function saveHookDataIfStatusAcceptable(PuppeteerReplay $puppeteerReplay, array $hookBodyData): void
    {
        if ($puppeteerReplay->getStatus() !== PuppeteerReplayStatusType::ERROR && isset($hookBodyData["screenshot"]) && isset($hookBodyData["content"])) {

            $myRecord = new PuppeteerReplayHookRecord();
            $myRecord->setReplay($puppeteerReplay);
            $myRecord->setStep(isset($hookBodyData["step"]) ? json_encode($hookBodyData["step"]) : '');
            $myRecord->setScreenshot($hookBodyData["screenshot"]);
            $myRecord->setContent($hookBodyData["content"]);
            $myRecord->setPhase($hookBodyData["phase"]);
            $this->entityManager->persist($myRecord);
            $this->entityManager->flush();

        } else {
            throw new BadRequestHttpException(serialize($hookBodyData));
        }
    }

    private function checkBodyData(array $hookBodyData): bool
    {
        return isset($hookBodyData["phase"]) && isset($hookBodyData["instanceID"]);
    }

    private function updateLifeCycle(PuppeteerReplay $puppeteerReplay, $phase, ?string $errorIfExist = NULL): PuppeteerReplay
    {
        $puppeteerReplayStatus = match ($phase) {
            default => PuppeteerReplayStatusType::UPLOAD,
            'beforeEachStep', 'afterEachStep' => PuppeteerReplayStatusType::PROCESSING,
            'afterAllSteps' => PuppeteerReplayStatusType::COMPLETED,
            'error' => PuppeteerReplayStatusType::ERROR,
        };
        $puppeteerReplay->setStatus($puppeteerReplayStatus);

        if ($puppeteerReplayStatus === PuppeteerReplayStatusType::ERROR && $errorIfExist !== NULL) {
            $puppeteerReplay->setLastErrorMessage($errorIfExist);
        }

        if ($puppeteerReplayStatus === PuppeteerReplayStatusType::COMPLETED) {
            $this->whenStatusCompleted($puppeteerReplay);
        }

        $this->entityManager->persist($puppeteerReplay);
        $this->entityManager->flush();
        return $puppeteerReplay;
    }

    private function whenStatusCompleted(PuppeteerReplay $puppeteerReplay): void
    {

    }

}