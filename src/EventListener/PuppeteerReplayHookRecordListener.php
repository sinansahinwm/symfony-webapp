<?php namespace App\EventListener;

use App\Entity\PuppeteerReplayHookRecord;
use App\Service\DomContentFramerService;
use App\Service\NotificationService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Exception;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use function Symfony\Component\Translation\t;

#[AsEntityListener(event: Events::prePersist, method: "prePersist", entity: PuppeteerReplayHookRecord::class)]
class PuppeteerReplayHookRecordListener
{

    public function __construct(private DomContentFramerService $domContentFramerService, private NotificationService $notificationService, private UrlGeneratorInterface $urlGenerator)
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

        // Send afterAllSteps Notification
        if ($puppeteerReplayHookRecord->getPhase() === "afterAllSteps") {
            $hookCreatedBy = $puppeteerReplayHookRecord->getReplay()->getCreatedBy();
            $hookActionUrl = $this->urlGenerator->generate("app_admin_puppeteer_replay_show", ["puppeteerReplay" => $puppeteerReplayHookRecord->getReplay()->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
            $translatedNotificationMessage = t("Chrome Aktarıcısı hizmeti başarıyla tamamlandı.");
            $this->notificationService->setMessage($translatedNotificationMessage)->release($hookCreatedBy, $hookActionUrl);
        }
    }


}