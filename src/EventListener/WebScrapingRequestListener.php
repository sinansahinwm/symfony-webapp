<?php namespace App\EventListener;

use App\Config\WebScrapingRequestStatusType;
use App\Entity\WebScrapingRequest;
use App\Message\ProccessWebScrapingRequestMessage;
use App\RemoteEvent\FirebaseScraperWebhookConsumer;
use App\Repository\WebScrapingRequestRepository;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEntityListener(event: Events::prePersist, method: 'webScrapingRequestPrePersist', entity: WebScrapingRequest::class)]
#[AsEntityListener(event: Events::postPersist, method: 'webScrapingRequestPostPersist', entity: WebScrapingRequest::class)]
class WebScrapingRequestListener
{

    const CACHE_TIME_MINUTES = 60;
    const DEFAULT_SCRAPER_WEBHOOK_ROUTE = "firebase_scraper";

    public function __construct(
        private MessageBusInterface            $messageBus,
        private UrlGeneratorInterface          $urlGenerator,
        private EntityManagerInterface         $entityManager,
        private WebScrapingRequestRepository   $webScrapingRequestRepository,
        private FirebaseScraperWebhookConsumer $firebaseScraperWebhookConsumer
    )
    {
    }

    public function webScrapingRequestPrePersist(WebScrapingRequest $webScrapingRequest, PrePersistEventArgs $myEvent): void
    {
        // If Newly Created -> Set Webhook URL
        if ($webScrapingRequest->getStatus() === WebScrapingRequestStatusType::NEWLY_CREATED) {

            // Create Webhook URL
            $webhookURL = $this->prepareWebhookUrlForWebScrapingRequest();
            $webScrapingRequest->setWebhookUrl($webhookURL);

        }
    }

    public function webScrapingRequestPostPersist(WebScrapingRequest $webScrapingRequest, PostPersistEventArgs $myEvent): void
    {

        // If Newly Created -> Send To Proccess
        if ($webScrapingRequest->getStatus() === WebScrapingRequestStatusType::NEWLY_CREATED) {

            // Check Cache Exist
            $cachedObject = $this->requestCacheExist($webScrapingRequest);

            if ($cachedObject !== FALSE) {

                // Copy Data to Requested Object
                $webScrapingRequest->setWebhookUrl($cachedObject->getWebhookUrl());
                $webScrapingRequest->setStatus($cachedObject->getStatus());
                $webScrapingRequest->setConsumedScreenshot($cachedObject->getConsumedScreenshot());
                $webScrapingRequest->setConsumedContent($cachedObject->getConsumedContent());
                $webScrapingRequest->setConsumedUrl($cachedObject->getConsumedUrl());
                $webScrapingRequest->setConsumedRemoteStatus($cachedObject->getConsumedRemoteStatus());
                $webScrapingRequest->setCompletedHandle($cachedObject->getCompletedHandle());
                $webScrapingRequest->setSteps($cachedObject->getSteps());
                $webScrapingRequest->setConsumedAt(new DateTimeImmutable());
                $webScrapingRequest->setLastErrorMessage($cachedObject->getLastErrorMessage());
                $webScrapingRequest->setXhrlog($cachedObject->getXhrlog());

                // Persist Cached Request
                $this->entityManager->persist($webScrapingRequest);
                $this->entityManager->flush();

                // Handle If Completed
                $this->firebaseScraperWebhookConsumer->handleWebScrapingRequestIfCompleted($webScrapingRequest);

            } else {

                // Dispatch Web Scraping Request Message
                $myMessage = new ProccessWebScrapingRequestMessage($webScrapingRequest->getId());
                $this->messageBus->dispatch($myMessage);

            }


        }

    }

    private function prepareWebhookUrlForWebScrapingRequest(): string
    {
        return $this->urlGenerator->generate('_webhook_controller', ['type' => self::DEFAULT_SCRAPER_WEBHOOK_ROUTE], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    private function requestCacheExist(WebScrapingRequest $webScrapingRequest): false|WebScrapingRequest
    {
        $myCachedRequest = $this->webScrapingRequestRepository->findOneBy([
            'navigate_url' => $webScrapingRequest->getNavigateUrl(),
            'status' => WebScrapingRequestStatusType::COMPLETED,
            'steps' => $webScrapingRequest->getSteps(),
        ]);

        if ($myCachedRequest instanceof WebScrapingRequest) {
            $timeNow = new DateTimeImmutable();
            $myCachedRequestCreatedAt = $myCachedRequest->getCreatedAt()->getTimestamp();
            $timeDiffMin = ($timeNow->getTimestamp() - $myCachedRequestCreatedAt) / 60;
            if ($timeDiffMin < self::CACHE_TIME_MINUTES) {
                return $myCachedRequest;
            }
        }

        return FALSE;
    }


}