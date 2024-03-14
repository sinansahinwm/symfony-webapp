<?php namespace App\EventListener;

use App\Controller\Webhook\PuppeteerReplayerWebhook;
use App\Entity\PuppeteerReplay;
use App\Service\PuppeteerReplayService;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Vich\UploaderBundle\Storage\StorageInterface;

#[AsEntityListener(event: Events::prePersist, method: "prePersist", entity: PuppeteerReplay::class)]
#[AsEntityListener(event: Events::postPersist, method: "postPersist", entity: PuppeteerReplay::class)]
class PuppeteerReplayListener
{
    public function __construct(private StorageInterface $storage, private LoggerInterface $logger, private PuppeteerReplayService $puppeteerReplayService, private PuppeteerReplayerWebhook $puppeteerReplayerWebhook, private Security $security)
    {
    }

    public function prePersist(PuppeteerReplay $puppeteerReplay)
    {
        if ($puppeteerReplay->getCreatedAt() === NULL) {
            $puppeteerReplay->setCreatedAt(new DateTimeImmutable());
        }
        if (($puppeteerReplay->getCreatedBy() === NULL) && ($this->security->getUser() !== NULL)) {
            $puppeteerReplay->setCreatedBy($this->security->getUser());
        }
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