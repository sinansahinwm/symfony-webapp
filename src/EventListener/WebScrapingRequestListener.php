<?php namespace App\EventListener;

use App\Config\WebScrapingRequestStatusType;
use App\Entity\WebScrapingRequest;
use App\Message\ProccessWebScrapingRequestMessage;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEntityListener(event: Events::postPersist, method: 'webScrapingRequestPostPersist', entity: WebScrapingRequest::class)]
class WebScrapingRequestListener
{

    const DEFAULT_SCRAPER_WEBHOOK_ROUTE = "firebase_scraper";

    public function __construct(private MessageBusInterface $messageBus, private UrlGeneratorInterface $urlGenerator, private EntityManagerInterface $entityManager)
    {
    }

    public function webScrapingRequestPostPersist(WebScrapingRequest $webScrapingRequest, PostPersistEventArgs $myEvent): void
    {
        // If Newly Created -> Send To Proccess
        if ($webScrapingRequest->getStatus() === WebScrapingRequestStatusType::NEWLY_CREATED) {

            // Pick Endpoint
            $webhookURL = $this->prepareWebhookUrlForWebScrapingRequest();
            $webScrapingRequest->setWebhookUrl($webhookURL);
            $this->entityManager->persist($webScrapingRequest);
            $this->entityManager->flush();

            $myMessage = new ProccessWebScrapingRequestMessage($webScrapingRequest->getId());
            $this->messageBus->dispatch($myMessage);
        }
    }

    private function prepareWebhookUrlForWebScrapingRequest(): string
    {
        return $this->urlGenerator->generate('_webhook_controller', ['type' => self::DEFAULT_SCRAPER_WEBHOOK_ROUTE], UrlGeneratorInterface::ABSOLUTE_URL);
    }


}