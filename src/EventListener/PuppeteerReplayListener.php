<?php namespace App\EventListener;

use App\Controller\Webhook\PuppeteerReplayerWebhook;
use App\Entity\PuppeteerReplay;
use App\Service\PuppeteerReplayService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Exception;
use Psr\Log\LoggerInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

#[AsEntityListener(event: Events::postPersist, method: "postPersist", entity: PuppeteerReplay::class)]
class PuppeteerReplayListener
{
    public function __construct(private StorageInterface $storage, private LoggerInterface $logger, private PuppeteerReplayService $puppeteerReplayService, private PuppeteerReplayerWebhook $puppeteerReplayerWebhook)
    {
    }

    public function postPersist(PuppeteerReplay $puppeteerReplay): void
    {
        try {
            $resolvedFilePath = $this->storage->resolvePath($puppeteerReplay);

            $this->puppeteerReplayService
                ->setRecordPath($resolvedFilePath)
                ->setWebhook($this->puppeteerReplayerWebhook)
                ->setInstanceID($puppeteerReplay->getId())
                ->play();

        } catch (Exception $exception) {
            $this->logger->error($exception);
        }
    }

}