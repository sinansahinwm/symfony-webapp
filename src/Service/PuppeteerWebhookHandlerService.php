<?php namespace App\Service;

use App\Entity\PuppeteerReplay;
use App\Repository\PuppeteerReplayRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PuppeteerWebhookHandlerService
{
    public function __construct(private PuppeteerReplayRepository $puppeteerReplayRepository)
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
            }
        } else {
            throw new BadRequestHttpException();
        }
    }

    private function checkBodyData(array $hookBodyData): bool
    {
        return TRUE;
    }
}