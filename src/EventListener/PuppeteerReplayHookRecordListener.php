<?php namespace App\EventListener;

use App\Entity\PuppeteerReplayHookRecord;
use App\Service\DomContentFramerService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Exception;

#[AsEntityListener(event: Events::prePersist, method: "prePersist", entity: PuppeteerReplayHookRecord::class)]
class PuppeteerReplayHookRecordListener
{

    public function __construct(private DomContentFramerService $domContentFramerService)
    {
    }

    public function prePersist(PuppeteerReplayHookRecord $puppeteerReplayHookRecord): void
    {
        try {
            $framedContent = $this->domContentFramerService->setHtml($puppeteerReplayHookRecord->getContent())->setUrlSchemeSource($puppeteerReplayHookRecord->getInitialPageUrl())->getFramedContent();
        } catch (Exception) {
            return;
        }
        $puppeteerReplayHookRecord->setContent($framedContent);
    }


}